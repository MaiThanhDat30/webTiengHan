<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\LearningLog;
use App\Models\UserVocabProgress;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Display the user's profile dashboard.
     */
    public function index(): View
    {
        $userId = auth()->id();

        /* =========================
       📘 TỔNG QUAN
    ========================= */

        $totalLearned = LearningLog::where('user_id', $userId)
            ->where('action', 'learn')
            ->distinct('vocabulary_id')
            ->count('vocabulary_id');

        $totalReviews = LearningLog::where('user_id', $userId)
            ->where('action', 'review')
            ->count();

        $dueToday = UserVocabProgress::where('user_id', $userId)
            ->where('next_review_at', '<=', now())
            ->count();

        /* =========================
       🧠 MỨC ĐỘ NHỚ
    ========================= */

        $memoryLevels = LearningLog::select(
            'vocabulary_id',
            DB::raw('MAX(`interval`) as max_interval')
        )
            ->where('user_id', $userId)
            ->where('action', 'review')
            ->groupBy('vocabulary_id')
            ->get()
            ->groupBy(fn($item) => match (true) {
                $item->max_interval >= 14 => 'long',
                $item->max_interval >= 3  => 'mid',
                default => 'new',
            });

        /* =========================
       📚 TỪ ĐÃ HỌC & ÔN (PAGINATION)
    ========================= */

        $words = LearningLog::query()
            ->where('learning_logs.user_id', $userId)
            ->leftJoin('vocabularies', 'learning_logs.vocabulary_id', '=', 'vocabularies.id')
            ->select(
                'learning_logs.vocabulary_id',
                'vocabularies.word_kr',
                'vocabularies.word_vi',

                DB::raw("COUNT(CASE WHEN learning_logs.action = 'review' THEN 1 END) as review_count"),
                DB::raw("COUNT(CASE WHEN learning_logs.result = 'correct' THEN 1 END) as correct_count"),
                DB::raw("COUNT(CASE WHEN learning_logs.result = 'wrong' THEN 1 END) as wrong_count"),

                DB::raw("MIN(learning_logs.created_at) as first_learned_at"),
                DB::raw("MAX(learning_logs.created_at) as last_activity_at")
            )
            ->groupBy(
                'learning_logs.vocabulary_id',
                'vocabularies.word_kr',
                'vocabularies.word_vi'
            )
            ->orderByDesc('last_activity_at')
            ->paginate(5); // 👈 SỐ TỪ / TRANG (tuỳ chỉnh)
        $badges = [];

        if ($totalReviews >= 100) $badges[] = '🔥 Chăm học';
        if (data_get($memoryLevels, 'long', collect())->count() >= 10) $badges[] = '🧠 Trí nhớ tốt';
        if ($totalLearned < 20) $badges[] = '🐣 Người mới';

        // ⏳ TỪ SẮP QUAY LẠI (1–3 NGÀY TỚI)
        $upcomingReviews = UserVocabProgress::with('vocabulary')
            ->where('user_id', $userId)
            ->whereBetween('next_review_at', [
                now()->addMinute(),        // sau hiện tại
                now()->addDays(3)          // trong 3 ngày tới
            ])
            ->orderBy('next_review_at')
            ->limit(5)
            ->get();

        return view('profile.index', compact(
            'totalLearned',
            'totalReviews',
            'dueToday',
            'memoryLevels',
            'words',
            'badges',
            'upcomingReviews'
        ));
    }
    public function markLearned(Request $request, int $vocabId)
{
    $userId = auth()->id();

    // ✅ Kiểm tra xem từ này đã từng được "learn" chưa
    $alreadyLearned = LearningLog::where('user_id', $userId)
        ->where('vocabulary_id', $vocabId)
        ->where('action', 'learn')
        ->exists();

    // ❌ Nếu đã học rồi → không làm gì cả
    if ($alreadyLearned) {
        return response()->json([
            'status' => 'exists',
            'message' => 'Từ này đã được tính là đã học'
        ]);
    }

    // ✅ Nếu CHƯA học → ghi log learn
    LearningLog::create([
        'user_id'       => $userId,
        'vocabulary_id' => $vocabId,
        'action'        => 'learn',
        'created_at'    => now(),
    ]);

    return response()->json([
        'status' => 'ok',
        'message' => 'Đã cộng vào tổng từ đã học'
    ]);
}

    /**
     * Show edit profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update profile info.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated');
    }

    /**
     * Delete account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
