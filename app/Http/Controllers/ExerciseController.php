<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\Mastery;
use App\Models\Subject;
use App\Services\FsrsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExerciseController extends Controller
{
    public function index(Request $request, Subject $subject): Response
    {
        abort_unless($subject->user_id === $request->user()->id, 403);

        $exercises = $subject->exercises()
            ->with('topic:id,name')
            ->orderBy('difficulty')
            ->get()
            ->map(fn ($e) => [
                ...$e->only('id', 'statement', 'difficulty', 'source_type', 'hint'),
                'topic_name' => $e->topic?->name,
                'attempts_count' => $e->attemptsBy($request->user()->id)->count(),
                'last_correct' => $e->attemptsBy($request->user()->id)->latest()->first()?->correct,
            ]);

        return Inertia::render('Exercises/Index', [
            'subject' => $subject->only('id', 'name', 'element'),
            'exercises' => $exercises,
        ]);
    }

    public function show(Request $request, Exercise $exercise): Response
    {
        $subject = $exercise->subject;
        abort_unless($subject->user_id === $request->user()->id, 403);

        $attempts = $exercise->attemptsBy($request->user()->id)
            ->latest()
            ->limit(5)
            ->get();

        return Inertia::render('Exercises/Show', [
            'subject' => $subject->only('id', 'name', 'element'),
            'exercise' => $exercise->only('id', 'statement', 'difficulty', 'hint', 'solution'),
            'attempts' => $attempts,
        ]);
    }

    public function attempt(Request $request, Exercise $exercise, FsrsService $fsrs): JsonResponse
    {
        $subject = $exercise->subject;
        abort_unless($subject->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'answer' => ['required', 'string', 'max:5000'],
        ]);

        $correct = $this->evaluateAnswer($exercise, $validated['answer']);

        $attempt = $exercise->attempts()->create([
            'user_id' => $request->user()->id,
            'answer' => $validated['answer'],
            'correct' => $correct,
            'feedback' => $correct
                ? 'Correcto.'
                : 'Incorrecto. Revisá tu respuesta.',
        ]);

        if ($exercise->topic_id) {
            $this->updateMastery($request->user()->id, $exercise->topic_id, $correct, $fsrs);
        }

        return response()->json([
            'correct' => $correct,
            'feedback' => $attempt->feedback,
            'solution' => $correct ? $exercise->solution : null,
        ]);
    }

    private function evaluateAnswer(Exercise $exercise, string $answer): bool
    {
        if (! $exercise->solution) {
            return false;
        }

        $normalized = fn (string $s) => mb_strtolower(trim(preg_replace('/\s+/', ' ', $s)));

        return $normalized($answer) === $normalized($exercise->solution);
    }

    private function updateMastery(int $userId, int $topicId, bool $correct, FsrsService $fsrs): void
    {
        $mastery = Mastery::firstOrCreate(
            ['user_id' => $userId, 'topic_id' => $topicId],
        );

        $rating = $correct ? 3 : 1;

        $result = $fsrs->schedule($mastery->toArray(), $rating);

        $mastery->update($result);
    }
}
