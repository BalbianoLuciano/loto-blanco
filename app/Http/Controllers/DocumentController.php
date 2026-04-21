<?php

namespace App\Http\Controllers;

use App\Jobs\ParseDocumentJob;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function store(Request $request, Subject $subject): RedirectResponse
    {
        abort_unless($subject->user_id === $request->user()->id, 403);

        $request->validate([
            'documents' => ['required', 'array', 'max:10'],
            'documents.*' => ['required', 'file', 'mimes:pdf', 'max:51200'],
        ]);

        foreach ($request->file('documents') as $file) {
            $path = $file->store("documents/{$subject->id}", 'local');

            $document = $subject->documents()->create([
                'title' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'original_filename' => $file->getClientOriginalName(),
                'disk_path' => $path,
                'mime_type' => $file->getMimeType(),
                'size_bytes' => $file->getSize(),
                'status' => 'pending',
            ]);

            ParseDocumentJob::dispatch($document);
        }

        return back();
    }
}
