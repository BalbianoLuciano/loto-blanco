<?php

namespace App\Jobs;

use App\Models\Document;
use App\Services\EmbeddingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmbedChunksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 120;

    public function __construct(
        public Document $document,
    ) {}

    public function handle(EmbeddingService $embedder): void
    {
        try {
            $chunks = $this->document->chunks()->whereDoesntHave('embedding')->get();

            if ($chunks->isEmpty()) {
                $this->document->update(['status' => 'ready']);

                return;
            }

            $texts = $chunks->pluck('content')->all();
            $vectors = $embedder->embed($texts);

            DB::transaction(function () use ($chunks, $vectors) {
                foreach ($chunks as $i => $chunk) {
                    $vectorStr = '['.implode(',', $vectors[$i]).']';
                    DB::statement(
                        'INSERT INTO embeddings (document_chunk_id, vector, created_at, updated_at) VALUES (?, ?::vector, NOW(), NOW())',
                        [$chunk->id, $vectorStr],
                    );
                }
            });

            $this->document->update(['status' => 'ready']);

            ExtractExercisesJob::dispatch($this->document);
        } catch (\Throwable $e) {
            Log::error('Embedding failed', [
                'document_id' => $this->document->id,
                'error' => $e->getMessage(),
            ]);

            $this->document->update(['status' => 'embed_failed']);

            throw $e;
        }
    }
}
