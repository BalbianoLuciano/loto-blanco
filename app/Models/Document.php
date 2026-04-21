<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Document extends Model
{
    protected $fillable = [
        'subject_id',
        'topic_id',
        'title',
        'original_filename',
        'disk_path',
        'mime_type',
        'size_bytes',
        'status',
        'parsed_at',
    ];

    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
            'parsed_at' => 'datetime',
        ];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function chunks(): HasMany
    {
        return $this->hasMany(DocumentChunk::class)->orderBy('chunk_index');
    }

    public function embeddings(): HasManyThrough
    {
        return $this->hasManyThrough(Embedding::class, DocumentChunk::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isReady(): bool
    {
        return $this->status === 'ready';
    }
}
