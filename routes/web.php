<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\FlashcardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubjectController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $user = request()->user();

    return Inertia::render('Dashboard', [
        'dueFlashcards' => $user->dueFlashcards()->count(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('subjects', SubjectController::class);
    Route::post('subjects/{subject}/documents', [DocumentController::class, 'store'])->name('subjects.documents.store');

    Route::get('subjects/{subject}/chat', [ChatController::class, 'show'])->name('subjects.chat');
    Route::post('subjects/{subject}/chat', [ChatController::class, 'ask'])->name('subjects.chat.ask');

    Route::get('subjects/{subject}/exercises', [ExerciseController::class, 'index'])->name('subjects.exercises');
    Route::get('exercises/{exercise}', [ExerciseController::class, 'show'])->name('exercises.show');
    Route::post('exercises/{exercise}/attempt', [ExerciseController::class, 'attempt'])->name('exercises.attempt');

    Route::get('flashcards/review', [FlashcardController::class, 'review'])->name('flashcards.review');
    Route::post('flashcards', [FlashcardController::class, 'store'])->name('flashcards.store');
    Route::post('flashcards/{flashcard}/review', [FlashcardController::class, 'submitReview'])->name('flashcards.submitReview');
});

require __DIR__.'/auth.php';
