<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlashcardReview extends Model
{
    protected $fillable = [
        'flashcard_id',
        'rating',
        'stability_before',
        'stability_after',
        'difficulty_before',
        'difficulty_after',
        'time_spent_ms',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'stability_before' => 'float',
            'stability_after' => 'float',
            'difficulty_before' => 'float',
            'difficulty_after' => 'float',
            'time_spent_ms' => 'integer',
            'reviewed_at' => 'datetime',
        ];
    }

    public function flashcard(): BelongsTo
    {
        return $this->belongsTo(Flashcard::class);
    }
}
