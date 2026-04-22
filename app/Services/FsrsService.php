<?php

namespace App\Services;

use Carbon\Carbon;

class FsrsService
{
    // FSRS-5 default parameters
    private const W = [
        0.4072, 1.1829, 3.1262, 15.4722,  // w0-w3: initial stability for Again/Hard/Good/Easy
        7.2102, 0.5316, 1.0651, 0.0589,    // w4-w7
        1.5330, 0.1544, 1.0095, 1.9279,    // w8-w11
        0.1117, 0.2979, 2.2698, 0.2315,    // w12-w15
        2.9898, 0.5164, 0.5780,             // w16-w18
    ];

    private const DECAY = -0.5;
    private const FACTOR = 0.9 ** (1 / self::DECAY) - 1;

    public function schedule(array $card, int $rating): array
    {
        $state = $card['state'] ?? 'new';
        $stability = (float) ($card['stability'] ?? 0);
        $difficulty = (float) ($card['difficulty'] ?? 0.3);
        $reps = (int) ($card['reps'] ?? 0);
        $lapses = (int) ($card['lapses'] ?? 0);

        $now = Carbon::now();

        if ($state === 'new') {
            return $this->scheduleNew($rating, $now);
        }

        $elapsedDays = isset($card['last_review_at'])
            ? max(0, Carbon::parse($card['last_review_at'])->floatDiffInDays($now))
            : 0;

        $retrievability = $this->retrievability($elapsedDays, $stability);

        $newDifficulty = $this->nextDifficulty($difficulty, $rating);
        $newDifficulty = max(0.01, min(0.99, $newDifficulty));

        if ($rating === 1) {
            $newStability = $this->stabilityAfterFail($newDifficulty, $stability, $retrievability);
            $lapses++;
            $newState = 'review';
        } else {
            $newStability = $this->stabilityAfterSuccess($newDifficulty, $stability, $retrievability, $rating);
            $newState = 'review';
        }

        $newStability = max(0.1, $newStability);
        $interval = $this->nextInterval($newStability);
        $dueAt = $now->copy()->addDays((int) ceil($interval));

        return [
            'stability' => round($newStability, 4),
            'difficulty' => round($newDifficulty, 4),
            'reps' => $reps + 1,
            'lapses' => $lapses,
            'state' => $newState,
            'due_at' => $dueAt,
            'last_review_at' => $now,
        ];
    }

    private function scheduleNew(int $rating, Carbon $now): array
    {
        $stability = self::W[min($rating - 1, 3)];
        $difficulty = self::W[4] - exp(self::W[5] * ($rating - 1)) + 1;
        $difficulty = max(0.01, min(0.99, $difficulty));

        $interval = $this->nextInterval($stability);
        $dueAt = $now->copy()->addDays(max(1, (int) ceil($interval)));

        $lapses = $rating === 1 ? 1 : 0;

        return [
            'stability' => round($stability, 4),
            'difficulty' => round($difficulty, 4),
            'reps' => 1,
            'lapses' => $lapses,
            'state' => 'review',
            'due_at' => $dueAt,
            'last_review_at' => $now,
        ];
    }

    private function retrievability(float $elapsedDays, float $stability): float
    {
        if ($stability <= 0) {
            return 0;
        }

        return (1 + self::FACTOR * $elapsedDays / $stability) ** self::DECAY;
    }

    private function nextDifficulty(float $d, int $rating): float
    {
        $delta = -(self::W[6] * ($rating - 3));

        return self::W[7] * self::W[4] + (1 - self::W[7]) * ($d + $delta);
    }

    private function stabilityAfterSuccess(float $d, float $s, float $r, int $rating): float
    {
        $hardPenalty = $rating === 2 ? self::W[15] : 1.0;
        $easyBonus = $rating === 4 ? self::W[16] : 1.0;

        return $s * (
            1 + exp(self::W[8])
            * (11 - $d)
            * $s ** (-self::W[9])
            * (exp((1 - $r) * self::W[10]) - 1)
            * $hardPenalty
            * $easyBonus
        );
    }

    private function stabilityAfterFail(float $d, float $s, float $r): float
    {
        return self::W[11]
            * $d ** (-self::W[12])
            * (($s + 1) ** self::W[13] - 1)
            * exp((1 - $r) * self::W[14]);
    }

    private function nextInterval(float $stability): float
    {
        return max(1, $stability * 9);
    }

    public static function ratingLabel(int $rating): string
    {
        return match ($rating) {
            1 => 'again',
            2 => 'hard',
            3 => 'good',
            4 => 'easy',
            default => 'unknown',
        };
    }
}
