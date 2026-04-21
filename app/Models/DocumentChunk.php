<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DocumentChunk extends Model
{
    protected $fillable = [
        'document_id',
        'chunk_index',
        'content',
        'metadata',
        'page_number',
        'token_count',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'chunk_index' => 'integer',
            'page_number' => 'integer',
            'token_count' => 'integer',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function embedding(): HasOne
    {
        return $this->hasOne(Embedding::class);
    }
}
