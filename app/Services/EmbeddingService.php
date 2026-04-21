<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class EmbeddingService
{
    /**
     * @param  string[]  $texts
     * @return array<int, float[]>
     */
    public function embed(array $texts): array
    {
        $apiKey = config('services.openai.api_key');

        $response = Http::withToken($apiKey)
            ->timeout(60)
            ->post('https://api.openai.com/v1/embeddings', [
                'model' => 'text-embedding-3-small',
                'input' => $texts,
            ]);

        $response->throw();

        return collect($response->json('data'))
            ->sortBy('index')
            ->pluck('embedding')
            ->all();
    }

    /**
     * @return float[]
     */
    public function embedSingle(string $text): array
    {
        return $this->embed([$text])[0];
    }
}
