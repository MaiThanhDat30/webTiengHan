<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VocabularyController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\SrsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TopikController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('home');
})->name('home');

/**
 * Dashboard – bắt buộc login + verify email
 */
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect()->route('dashboard')
        ->with('success', 'Xác thực email thành công!');
})->middleware(['signed'])->name('verification.verify');

/**
 * TOÀN BỘ CHỨC NĂNG HỌC
 * ➜ Login + Verify email mới dùng được
 */
Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Vocabulary
    |--------------------------------------------------------------------------
    */
    Route::get('/vocab', [VocabularyController::class, 'index'])
        ->name('vocab.index');

    Route::get('/vocab/create', [VocabularyController::class, 'create'])
        ->name('vocab.create');

    Route::post('/vocab', [VocabularyController::class, 'store'])
        ->name('vocab.store');

    /*
    |--------------------------------------------------------------------------
    | Topics
    |--------------------------------------------------------------------------
    */
    Route::get('/topics', [TopicController::class, 'index'])
        ->name('topics.index');

    Route::get('/topics/{id}', [TopicController::class, 'show'])
        ->name('topics.show');

    Route::get('/topics/{id}/flashcard', [TopicController::class, 'flashcard'])
        ->name('topics.flashcard');

    Route::get('/topics/{topic}/vocabularies', [VocabularyController::class, 'index'])
        ->name('topics.vocabularies');

    /*
    |--------------------------------------------------------------------------
    | SRS – Ôn tập
    |--------------------------------------------------------------------------
    */
    Route::post('/srs/answer', [SrsController::class, 'answer'])
        ->name('srs.answer');

    Route::get('/review', [SrsController::class, 'review'])
        ->name('srs.review');

    Route::post('/review/answer', [SrsController::class, 'reviewAnswer'])
        ->name('srs.review.answer');

    Route::get('/review/next', [SrsController::class, 'nextReview'])
        ->name('srs.review.next');

    Route::get('/review/{progress}', [SrsController::class, 'reviewCard'])
        ->name('srs.card');
    Route::post('/srs/save', [SrsController::class, 'saveForReview'])
        ->name('srs.save')
        ->middleware('auth');
    Route::post('/srs/toggle', [SrsController::class, 'toggle'])
        ->name('srs.toggle');
    /*
    |--------------------------------------------------------------------------
    | TOPIK
    |--------------------------------------------------------------------------
    */
    Route::get('/topiks', [TopikController::class, 'index'])
        ->name('topiks.index');
});



/**
 * Auth routes (login, register, verify email, reset password)
 */
require __DIR__ . '/auth.php';
