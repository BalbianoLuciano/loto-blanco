<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ChatController extends Controller
{
    public function show(Request $request, Subject $subject): Response
    {
        abort_unless($subject->user_id === $request->user()->id, 403);

        $messages = $subject->chatMessages()
            ->where('user_id', $request->user()->id)
            ->oldest()
            ->limit(50)
            ->get();

        return Inertia::render('Chat/Show', [
            'subject' => $subject->only('id', 'name', 'element'),
            'messages' => $messages,
        ]);
    }

    public function ask(Request $request, Subject $subject, ChatService $chat): JsonResponse
    {
        abort_unless($subject->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'question' => ['required', 'string', 'max:2000'],
        ]);

        $result = $chat->ask($subject, $request->user()->id, $validated['question']);

        return response()->json($result);
    }
}
