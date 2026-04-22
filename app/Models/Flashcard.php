<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Flashcard extends Model
{
    protected $fillable = [
        'user_id',
        'topic_id',
        'document_chunk_id',
        'front',
        'back',
        'source_type',
        'stability',
        'difficulty',
        'reps',
        'lapses',
        'state',
        'due_at',
        'last_review_at',
    ];

    protected function casts(): array
    {
        return [
            'stability' => 'float',
            'difficulty' => 'float',
            'reps' => 'integer',
            'lapses' => 'integer',
            'due_at' => 'datetime',
            'last_review_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function chunk(): BelongsTo
    {
        return $this->belongsTo(DocumentChunk::class, 'document_chunk_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(FlashcardReview::class)->latest('reviewed_at');
    }

    public function isDue(): bool
    {
        return $this->state === 'new' || $this->due_at === null || $this->due_at->isPast();
    }
}
