<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExerciseAttempt extends Model
{
    protected $fillable = [
        'exercise_id',
        'user_id',
        'answer',
        'correct',
        'time_spent_seconds',
        'feedback',
    ];

    protected function casts(): array
    {
        return [
            'correct' => 'boolean',
            'time_spent_seconds' => 'integer',
        ];
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
