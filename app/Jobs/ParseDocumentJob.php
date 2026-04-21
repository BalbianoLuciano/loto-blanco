<?php

namespace App\Jobs;

use App\Models\Document;
use App\Services\DocumentParser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ParseDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public Document $document,
    ) {}

    public function handle(DocumentParser $parser): void
    {
        $this->document->update(['status' => 'processing']);

        try {
            $chunks = $parser->parse($this->document);

            foreach ($chunks as $index => $chunk) {
                $this->document->chunks()->create([
                    'chunk_index' => $index,
                    'content' => $chunk['content'],
                    'metadata' => $chunk['metadata'] ?? null,
                    'page_number' => $chunk['page_number'] ?? null,
                    'token_count' => $chunk['token_count'] ?? null,
                ]);
            }

            $this->document->update([
                'status' => 'parsed',
                'parsed_at' => now(),
            ]);

            EmbedChunksJob::dispatch($this->document);
        } catch (\Throwable $e) {
            Log::error('Document parsing failed', [
                'document_id' => $this->document->id,
                'error' => $e->getMessage(),
            ]);

            $this->document->update(['status' => 'failed']);

            throw $e;
        }
    }
}
