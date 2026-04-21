<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class SubjectController extends Controller
{
    public function index(Request $request): Response
    {
        $subjects = $request->user()
            ->subjects()
            ->where('archived', false)
            ->withCount(['documents', 'topics'])
            ->latest()
            ->get();

        return Inertia::render('Subjects/Index', [
            'subjects' => $subjects,
        ]);
    }

    public function create(): Response
    {
        $templates = DB::table('subject_templates')->get();

        return Inertia::render('Subjects/Create', [
            'templates' => $templates,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'element' => ['required', 'string', 'in:water,fire,earth,air,lotus'],
            'color' => ['required', 'string', 'max:7'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $request->user()->subjects()->create($validated);

        return redirect()->route('subjects.index');
    }

    public function show(Request $request, Subject $subject): Response
    {
        $this->authorizeSubject($request, $subject);

        $subject->load(['topics', 'documents' => fn ($q) => $q->latest()]);

        return Inertia::render('Subjects/Show', [
            'subject' => $subject,
        ]);
    }

    public function edit(Request $request, Subject $subject): Response
    {
        $this->authorizeSubject($request, $subject);

        return Inertia::render('Subjects/Edit', [
            'subject' => $subject,
        ]);
    }

    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $this->authorizeSubject($request, $subject);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'element' => ['required', 'string', 'in:water,fire,earth,air,lotus'],
            'color' => ['required', 'string', 'max:7'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $subject->update($validated);

        return redirect()->route('subjects.show', $subject);
    }

    public function destroy(Request $request, Subject $subject): RedirectResponse
    {
        $this->authorizeSubject($request, $subject);

        $subject->delete();

        return redirect()->route('subjects.index');
    }

    private function authorizeSubject(Request $request, Subject $subject): void
    {
        abort_unless($subject->user_id === $request->user()->id, 403);
    }
}
