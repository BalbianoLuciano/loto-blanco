<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mastery extends Model
{
    protected $table = 'mastery';

    protected $fillable = [
        'user_id',
        'topic_id',
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

    public function isDue(): bool
    {
        return $this->state === 'new' || $this->due_at === null || $this->due_at->isPast();
    }
}
