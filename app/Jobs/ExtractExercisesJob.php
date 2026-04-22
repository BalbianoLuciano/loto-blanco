<?php

namespace App\Jobs;

use App\Models\Document;
use App\Services\ChatService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExtractExercisesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public function __construct(
        public Document $document,
    ) {}

    public function handle(): void
    {
        $chunks = $this->document->chunks()->get();
        if ($chunks->isEmpty()) {
            return;
        }

        $config = config('services.moonshot');
        $content = $chunks->pluck('content')->implode("\n\n---\n\n");

        $prompt = <<<PROMPT
Analizá el siguiente material académico y extraé todos los ejercicios/problemas que encuentres.
Para cada ejercicio devolvé un JSON array con objetos que tengan:
- "statement": el enunciado completo del ejercicio
- "solution": la solución si está disponible, o null
- "hint": una pista breve, o null
- "difficulty": un número del 1 al 5 (1=muy fácil, 5=muy difícil)

Devolvé SOLO el JSON array, sin texto adicional.

Material:
{$content}
PROMPT;

        try {
            $response = Http::withToken($config['api_key'])
                ->timeout(120)
                ->post("{$config['base_url']}/chat/completions", [
                    'model' => $config['model'],
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.1,
                    'max_tokens' => 4096,
                ]);

            $response->throw();
            $text = $response->json('choices.0.message.content');

            $text = preg_replace('/^```json\s*/', '', $text);
            $text = preg_replace('/\s*```$/', '', $text);

            $exercises = json_decode($text, true);
            if (! is_array($exercises)) {
                return;
            }

            foreach ($exercises as $ex) {
                if (empty($ex['statement'])) {
                    continue;
                }

                $this->document->subject->exercises()->create([
                    'topic_id' => $this->document->topic_id,
                    'document_id' => $this->document->id,
                    'source_type' => 'extracted',
                    'difficulty' => max(1, min(5, (int) ($ex['difficulty'] ?? 3))),
                    'statement' => $ex['statement'],
                    'solution' => $ex['solution'] ?? null,
                    'hint' => $ex['hint'] ?? null,
                    'verified_by' => 'llm',
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Exercise extraction failed', [
                'document_id' => $this->document->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
