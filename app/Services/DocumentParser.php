<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class DocumentParser
{
    /**
     * @return array<int, array{content: string, page_number: ?int, token_count: ?int, metadata: ?array}>
     */
    public function parse(Document $document): array
    {
        $pythonSvcUrl = config('services.python_svc.url');

        if ($pythonSvcUrl) {
            return $this->parseViaPythonService($document, $pythonSvcUrl);
        }

        return $this->parseLocally($document);
    }

    private function parseViaPythonService(Document $document, string $url): array
    {
        $filePath = Storage::disk('local')->path($document->disk_path);

        $response = Http::timeout(120)
            ->attach('file', file_get_contents($filePath), $document->original_filename)
            ->post("{$url}/parse");

        $response->throw();

        return $response->json('chunks');
    }

    /**
     * Fallback: simple text extraction + chunking using smalot/pdfparser.
     * Good enough for dev; python-svc with Unstructured will replace this.
     */
    private function parseLocally(Document $document): array
    {
        $filePath = Storage::disk('local')->path($document->disk_path);
        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);

        $chunks = [];
        $pages = $pdf->getPages();

        foreach ($pages as $pageIndex => $page) {
            $text = trim($page->getText());
            if ($text === '') {
                continue;
            }

            foreach ($this->chunkText($text, 800, 100) as $chunkText) {
                $chunks[] = [
                    'content' => $chunkText,
                    'page_number' => $pageIndex + 1,
                    'token_count' => (int) ceil(mb_strlen($chunkText) / 4),
                    'metadata' => ['source' => 'local_parser'],
                ];
            }
        }

        return $chunks;
    }

    /**
     * @return string[]
     */
    private function chunkText(string $text, int $maxChars, int $overlap): array
    {
        if (mb_strlen($text) <= $maxChars) {
            return [$text];
        }

        $chunks = [];
        $start = 0;
        $length = mb_strlen($text);

        while ($start < $length) {
            $chunk = mb_substr($text, $start, $maxChars);
            $chunks[] = trim($chunk);
            $start += $maxChars - $overlap;
        }

        return $chunks;
    }
}
