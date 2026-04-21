<?php

namespace App\Services;

use App\Models\ChatMessage;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ChatService
{
    public function __construct(
        private EmbeddingService $embedder,
    ) {}

    /**
     * @return array{answer: string, citations: array}
     */
    public function ask(Subject $subject, int $userId, string $question): array
    {
        ChatMessage::create([
            'subject_id' => $subject->id,
            'user_id' => $userId,
            'role' => 'user',
            'content' => $question,
        ]);

        $queryVector = $this->embedder->embedSingle($question);
        $vectorStr = '['.implode(',', $queryVector).']';

        $results = DB::select("
            SELECT dc.id, dc.content, dc.page_number, d.title as document_title,
                   1 - (e.vector <=> ?::vector) as similarity
            FROM embeddings e
            JOIN document_chunks dc ON dc.id = e.document_chunk_id
            JOIN documents d ON d.id = dc.document_id
            WHERE d.subject_id = ?
            ORDER BY e.vector <=> ?::vector
            LIMIT 5
        ", [$vectorStr, $subject->id, $vectorStr]);

        $context = collect($results)->map(fn ($r) => "[{$r->document_title}, p.{$r->page_number}]: {$r->content}")->implode("\n\n");

        $citations = collect($results)->map(fn ($r) => [
            'chunk_id' => $r->id,
            'document' => $r->document_title,
            'page' => $r->page_number,
            'similarity' => round($r->similarity, 3),
        ])->all();

        $answer = $this->callLlm($question, $context, $subject->name);

        $message = ChatMessage::create([
            'subject_id' => $subject->id,
            'user_id' => $userId,
            'role' => 'assistant',
            'content' => $answer,
            'citations' => $citations,
        ]);

        return [
            'answer' => $answer,
            'citations' => $citations,
            'message_id' => $message->id,
        ];
    }

    private function callLlm(string $question, string $context, string $subjectName): string
    {
        $config = config('services.moonshot');

        $systemPrompt = <<<PROMPT
Sos un asistente académico para la materia "{$subjectName}". Respondé en el mismo idioma que la pregunta del usuario.
Usá el contexto proporcionado para responder. Si la información no está en el contexto, decilo claramente.
Citá las fuentes usando [Documento, p.X] cuando uses información del contexto.
PROMPT;

        $response = Http::withToken($config['api_key'])
            ->timeout(60)
            ->post("{$config['base_url']}/chat/completions", [
                'model' => $config['model'],
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => "Contexto:\n{$context}\n\nPregunta: {$question}"],
                ],
                'temperature' => 0.3,
                'max_tokens' => 2048,
            ]);

        $response->throw();

        return $response->json('choices.0.message.content');
    }
}
