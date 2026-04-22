<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exercise extends Model
{
    protected $fillable = [
        'subject_id',
        'topic_id',
        'document_id',
        'source_type',
        'difficulty',
        'statement',
        'solution',
        'hint',
        'verified_by',
    ];

    protected function casts(): array
    {
        return [
            'difficulty' => 'integer',
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

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ExerciseAttempt::class);
    }

    public function attemptsBy(int $userId): HasMany
    {
        return $this->attempts()->where('user_id', $userId);
    }
}
