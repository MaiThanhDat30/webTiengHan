<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Controllers
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\{
    ProfileController,
    DashboardController,
    VocabularyController,
    TopicController,
    SrsController,
    TopikController,
    StudyTimeController
};

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => view('home'))->name('home');

/*
|--------------------------------------------------------------------------
| Authenticated + Verified Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::post('/study/ping', [StudyTimeController::class, 'ping'])
        ->name('study.ping');

    // ✅ PROFILE
    Route::get('/profile', [ProfileController::class, 'index'])
        ->name('profile.index');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

        
    Route::get('/vocab', [VocabularyController::class, 'index'])->name('vocab.index');
    Route::get('/vocab/create', [VocabularyController::class, 'create'])->name('vocab.create');
    Route::post('/vocab', [VocabularyController::class, 'store'])->name('vocab.store');

    Route::get(
        '/topics/{id}/flashcard/preload',
        [TopicController::class, 'preloadFlashcards']
    )->name('topics.flashcard.preload');
    Route::get('/topics', [TopicController::class, 'index'])->name('topics.index');
    Route::get('/topics/{id}', [TopicController::class, 'show'])->name('topics.show');
    Route::get('/topics/{id}/flashcard', [TopicController::class, 'flashcard'])->name('topics.flashcard');

    Route::get('/review', [SrsController::class, 'review'])->name('srs.review');
    Route::get('/review/next', [SrsController::class, 'nextReview'])->name('srs.review.next');
    Route::get('/review/{progress}', [SrsController::class, 'reviewCard'])->name('srs.card');

    Route::post('/srs/answer', [SrsController::class, 'answer'])->name('srs.answer');
    Route::post('/review/answer', [SrsController::class, 'reviewAnswer'])->name('srs.review.answer');
    Route::post('/srs/save', [SrsController::class, 'saveForReview'])->name('srs.save');
    Route::post('/srs/toggle', [SrsController::class, 'toggle'])->name('srs.toggle');
    Route::get('/srs/next', [SrsController::class, 'nextReview'])
        ->name('srs.next');
    Route::get('/srs/review/{progress}/json', [SrsController::class, 'reviewJson'])
        ->name('srs.review.json');

    Route::get('/topiks', [TopikController::class, 'index'])->name('topiks.index');
});

/*
|--------------------------------------------------------------------------
| Auth routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
