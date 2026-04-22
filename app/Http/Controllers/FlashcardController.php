<?php

namespace App\Http\Controllers;

use App\Models\Flashcard;
use App\Services\FsrsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FlashcardController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'front' => ['required', 'string', 'max:2000'],
            'back' => ['required', 'string', 'max:2000'],
            'topic_id' => ['nullable', 'exists:topics,id'],
            'document_chunk_id' => ['nullable', 'exists:document_chunks,id'],
            'source_type' => ['nullable', 'string', 'in:manual,highlight,llm'],
        ]);

        $request->user()->flashcards()->create([
            ...$validated,
            'source_type' => $validated['source_type'] ?? 'manual',
        ]);

        return back();
    }

    public function review(Request $request): Response
    {
        $cards = $request->user()->dueFlashcards()
            ->with('topic:id,name')
            ->limit(20)
            ->get()
            ->map(fn (Flashcard $f) => [
                ...$f->only('id', 'front', 'back', 'state', 'reps', 'stability', 'difficulty'),
                'topic_name' => $f->topic?->name,
            ]);

        $totalDue = $request->user()->dueFlashcards()->count();
        $totalCards = $request->user()->flashcards()->count();

        return Inertia::render('Flashcards/Review', [
            'cards' => $cards,
            'totalDue' => $totalDue,
            'totalCards' => $totalCards,
        ]);
    }

    public function submitReview(Request $request, Flashcard $flashcard, FsrsService $fsrs): JsonResponse
    {
        abort_unless($flashcard->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,4'],
            'time_spent_ms' => ['nullable', 'integer', 'min:0'],
        ]);

        $before = [
            'stability' => $flashcard->stability,
            'difficulty' => $flashcard->difficulty,
        ];

        $result = $fsrs->schedule($flashcard->toArray(), $validated['rating']);

        $flashcard->update($result);

        $flashcard->reviews()->create([
            'rating' => $validated['rating'],
            'stability_before' => $before['stability'],
            'stability_after' => $result['stability'],
            'difficulty_before' => $before['difficulty'],
            'difficulty_after' => $result['difficulty'],
            'time_spent_ms' => $validated['time_spent_ms'] ?? null,
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'next_due' => $result['due_at']->toDateTimeString(),
            'stability' => $result['stability'],
        ]);
    }
}
