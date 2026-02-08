<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

/**
 * MODELS
 */

use App\Models\LearningLog;
use App\Models\UserVocabProgress;
use App\Models\Idiom;
use App\Models\UserStreak;

class DashboardController extends Controller
{
    public function index()
    {
        /* =====================================================
         | USER
         ===================================================== */
        $user = Auth::user();
        $userId = $user->id;
        $today = now()->toDateString();

        /* =====================================================
         | 1️⃣ STREAK – DAILY STUDY (1 NGÀY CHỈ TÍNH 1 LẦN)
         ===================================================== */
        $streak = UserStreak::firstOrCreate(
            ['user_id' => $userId],
            [
                'current_streak' => 0,
                'longest_streak' => 0,
                'last_study_date' => null,
            ]
        );

        if ($streak->last_study_date !== $today) {
            if ($streak->last_study_date === now()->subDay()->toDateString()) {
                $streak->current_streak += 1;
            } else {
                $streak->current_streak = 1;
            }

            if ($streak->current_streak > $streak->longest_streak) {
                $streak->longest_streak = $streak->current_streak;
            }

            $streak->last_study_date = $today;
            $streak->save();
        }

        $currentStreak = $streak->current_streak;
        $longestStreak = $streak->longest_streak;
        $studiedToday = $streak->last_study_date === $today;

        /* =====================================================
         | 2️⃣ CACHE DATA NẶNG
         ===================================================== */
        $cacheKey = "dashboard_v4_user_{$userId}_{$today}";

        $data = Cache::remember($cacheKey, 300, function () use ($userId, $today) {

            // Tổng từ đã học
            $totalLearned = LearningLog::where('user_id', $userId)
                ->where('action', 'learn')
                ->distinct('vocabulary_id')
                ->count('vocabulary_id');

            // Từ cần ôn
            $needReview = UserVocabProgress::where('user_id', $userId)
                ->where('next_review_at', '<=', now())
                ->count();

            // Thống kê hôm nay (chỉ để phân tích – KHÔNG LÀM LEVEL)
            $todayStats = LearningLog::where('user_id', $userId)
                ->whereDate('created_at', $today)
                ->selectRaw("
                    SUM(CASE WHEN result = 'correct' THEN 1 ELSE 0 END) as correct,
                    SUM(CASE WHEN result = 'wrong' THEN 1 ELSE 0 END) as wrong
                ")
                ->first();

            $todayCorrect = $todayStats->correct ?? 0;
            $todayWrong = $todayStats->wrong ?? 0;

            // Biểu đồ 7 ngày
            $last7Days = LearningLog::where('user_id', $userId)
                ->where('created_at', '>=', now()->subDays(6))
                ->selectRaw("DATE(created_at) as date, COUNT(*) as total")
                ->groupByRaw("DATE(created_at)")
                ->orderBy('date')
                ->get();

            // Từ hay sai
            $problemVocabs = LearningLog::join(
                'vocabularies',
                'learning_logs.vocabulary_id',
                '=',
                'vocabularies.id'
            )
                ->where('learning_logs.user_id', $userId)
                ->selectRaw("
                    vocabularies.word_kr,
                    COUNT(*) as total,
                    SUM(CASE WHEN learning_logs.result = 'wrong' THEN 1 ELSE 0 END) as wrongs
                ")
                ->groupBy('vocabularies.word_kr')
                ->havingRaw("SUM(CASE WHEN learning_logs.result = 'wrong' THEN 1 ELSE 0 END) > 0")
                ->orderByDesc('wrongs')
                ->limit(5)
                ->get()
                ->map(function ($vocab) {
                    $vocab->tag = $vocab->wrongs >= 3 ? 'Hay quên' : 'Hay sai';
                    return $vocab;
                });

            // BXH global
            $globalWrongRanking = Cache::remember(
                'global_wrong_ranking',
                3600,
                fn() =>
                LearningLog::join(
                    'vocabularies',
                    'learning_logs.vocabulary_id',
                    '=',
                    'vocabularies.id'
                )
                    ->where('learning_logs.result', 'wrong')
                    ->selectRaw("
                        vocabularies.word_kr,
                        COUNT(*) as wrong_times
                    ")
                    ->groupBy('vocabularies.word_kr')
                    ->orderByDesc('wrong_times')
                    ->limit(5)
                    ->get()
            );

            return compact(
                'totalLearned',
                'needReview',
                'todayCorrect',
                'todayWrong',
                'last7Days',
                'problemVocabs',
                'globalWrongRanking'
            );
        });

        /* =====================================================
         | 3️⃣ ĐÁNH GIÁ TRÌNH ĐỘ (KHÔNG DÙNG ACCURACY)
         ===================================================== */
        $level = match (true) {
            $data['totalLearned'] < 100 => 'Mới bắt đầu',
            $longestStreak >= 30 && $data['totalLearned'] >= 1000 => 'Rất tốt',
            $currentStreak >= 7 && $data['totalLearned'] >= 500 => 'Tốt',
            $currentStreak >= 3 => 'Ổn định',
            default => 'Chưa ổn định',
        };

        /* =====================================================
         | 4️⃣ PERSONA – HÀNH VI HỌC
         ===================================================== */
        $persona = match (true) {
            !$studiedToday => 'Ngắt quãng',
            $data['needReview'] >= 30 => 'Quá tải',
            $currentStreak >= 10 => 'Kỷ luật cao',
            $currentStreak >= 5 => 'Chăm chỉ',
            default => 'Ổn định',
        };

        $personaMessage = match ($persona) {
            'Ngắt quãng' => 'Bạn đang học không đều. Chỉ cần 10–15 phút mỗi ngày là đủ 👍',
            'Quá tải' => 'Bạn có nhiều từ đến hạn ôn. Hôm nay nên ưu tiên ôn tập.',
            'Kỷ luật cao' => 'Bạn có kỷ luật học rất tốt 🔥 Giữ vững phong độ!',
            'Chăm chỉ' => 'Bạn học khá đều, cố thêm chút nữa nhé!',
            default => 'Tiến độ ổn định. Duy trì là sẽ tiến rất nhanh.',
        };

        /* =====================================================
         | 5️⃣ GỢI Ý LỘ TRÌNH
         ===================================================== */
        $suggestion = match ($persona) {
            'Quá tải' => 'Hôm nay nên ôn lại từ cũ, chưa nên học từ mới.',
            'Ngắt quãng' => 'Bắt đầu nhẹ với 5–10 từ để lấy lại thói quen.',
            default => 'Tiếp tục duy trì nhịp học hiện tại.',
        };

        /* =====================================================
         | 6️⃣ IDIOM GỢI Ý 
         ===================================================== */
        $seed = now()->toDateString();

        $idiomSuggestions = Cache::remember(
            "idiom_random_{$seed}",
            86400,
            fn() => Idiom::inRandomOrder()
                ->limit(5)
                ->get()
        );

        /* =====================================================
         | RETURN VIEW
         ===================================================== */
        return view('dashboard', array_merge(
            $data,
            compact(
                'currentStreak',
                'longestStreak',
                'studiedToday',
                'level',
                'persona',
                'personaMessage',
                'suggestion',
                'idiomSuggestions'
            )
        ));
    }
}
