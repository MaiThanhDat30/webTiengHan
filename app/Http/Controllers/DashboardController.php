<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

use App\Models\LearningLog;
use App\Models\UserVocabProgress;
use App\Models\Idiom;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user   = Auth::user();
        $userId = $user->id;
        $today  = today();

        /* =====================================================
         | 1️⃣ THỐNG KÊ NHANH
         ===================================================== */

        $totalLearned = Cache::remember(
            "total_learned_user_$userId",
            300,
            fn () =>
                LearningLog::where('user_id', $userId)
                    ->where('action', 'learn')
                    ->distinct('vocabulary_id')
                    ->count('vocabulary_id')
        );

        $needReview = Cache::remember(
            "need_review_user_$userId",
            300,
            fn () =>
                UserVocabProgress::where('user_id', $userId)
                    ->where('next_review_at', '<=', now())
                    ->count()
        );

        $todayActivity = LearningLog::where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->count();

        /* =====================================================
         | 2️⃣ ĐÚNG / SAI HÔM NAY
         ===================================================== */

        $todayStats = LearningLog::where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->selectRaw("
                SUM(CASE WHEN result = 'correct' THEN 1 ELSE 0 END) AS correct,
                SUM(CASE WHEN result = 'wrong' THEN 1 ELSE 0 END)   AS wrong
            ")
            ->first();

        $todayCorrect = $todayStats->correct ?? 0;
        $todayWrong   = $todayStats->wrong ?? 0;

        $totalReviews = $todayCorrect + $todayWrong;

        $accuracy = $totalReviews > 0
            ? round(($todayCorrect / $totalReviews) * 100)
            : 0;

        $level = match (true) {
            $accuracy < 50 => 'Yếu',
            $accuracy < 70 => 'Trung bình',
            $accuracy < 85 => 'Khá',
            default        => 'Tốt',
        };

        /* =====================================================
         | 3️⃣ BIỂU ĐỒ 7 NGÀY (CACHE)
         ===================================================== */

        $last7Days = Cache::remember(
            "last7days_user_$userId",
            300,
            function () use ($userId) {
                return LearningLog::where('user_id', $userId)
                    ->where('created_at', '>=', now()->subDays(6))
                    ->selectRaw("DATE(created_at) AS date, COUNT(*) AS total")
                    ->groupByRaw("DATE(created_at)")
                    ->orderBy('date')
                    ->get();
            }
        );

        /* =====================================================
         | 4️⃣ TỪ VỰNG HAY SAI / HAY QUÊN (CÁ NHÂN)
         ===================================================== */

        $problemVocabs = LearningLog::join(
                'vocabularies',
                'learning_logs.vocabulary_id',
                '=',
                'vocabularies.id'
            )
            ->leftJoin('user_vocab_progress', function ($join) use ($userId) {
                $join->on(
                        'learning_logs.vocabulary_id',
                        '=',
                        'user_vocab_progress.vocabulary_id'
                    )
                    ->where('user_vocab_progress.user_id', $userId);
            })
            ->where('learning_logs.user_id', $userId)
            ->groupBy(
                'learning_logs.vocabulary_id',
                'vocabularies.word_kr',
                'user_vocab_progress.next_review_at'
            )
            ->havingRaw("
                SUM(CASE WHEN learning_logs.result = 'wrong' THEN 1 ELSE 0 END) >= 2
            ")
            ->selectRaw("
                vocabularies.word_kr,
                SUM(CASE WHEN learning_logs.result = 'wrong' THEN 1 ELSE 0 END) AS wrongs,
                CASE
                    WHEN user_vocab_progress.next_review_at <= NOW()
                    THEN 'Hay quên'
                    ELSE 'Hay sai'
                END AS tag
            ")
            ->orderByDesc('wrongs')
            ->limit(5)
            ->get();

        /* =====================================================
         | 5️⃣ GỢI Ý LỘ TRÌNH (CÁ NHÂN HOÁ)
         ===================================================== */

        $suggestion = match (true) {
            $needReview >= 20 =>
                'Bạn đang có nhiều từ đến hạn ôn. Hôm nay nên ưu tiên ôn tập trước.',
            $accuracy < 60 =>
                'Độ chính xác còn thấp. Hãy giảm học từ mới và tăng ôn tập.',
            $totalLearned < 100 =>
                'Bạn đang ở giai đoạn nền tảng. Mỗi ngày học 10–15 từ là hợp lý.',
            default =>
                'Tiến độ rất tốt! Tiếp tục duy trì thói quen học đều đặn.',
        };

        /* =====================================================
         | 6️⃣ BXH TỪ KHÓ TOÀN HỆ THỐNG (CACHE 1H)
         ===================================================== */

        $globalWrongRanking = Cache::remember(
            'global_wrong_ranking',
            3600,
            function () {
                return LearningLog::join(
                        'vocabularies',
                        'learning_logs.vocabulary_id',
                        '=',
                        'vocabularies.id'
                    )
                    ->where('learning_logs.result', 'wrong')
                    ->groupBy('vocabularies.word_kr')
                    ->selectRaw('vocabularies.word_kr, COUNT(*) AS wrong_times')
                    ->orderByDesc('wrong_times')
                    ->limit(5)
                    ->get();
            }
        );

        /* =====================================================
         | 7️⃣ IDIOM / MẪU CÂU (CACHE 1 NGÀY)
         ===================================================== */

        $idiomSuggestions = Cache::remember(
            'idiom_suggestions',
            86400,
            fn () => Idiom::inRandomOrder()->limit(5)->get()
        );

        /* =====================================================
         | 8️⃣ VIEW
         ===================================================== */

        return view('dashboard', compact(
            'totalLearned',
            'needReview',
            'todayActivity',
            'todayCorrect',
            'todayWrong',
            'accuracy',
            'level',
            'last7Days',
            'problemVocabs',
            'suggestion',
            'globalWrongRanking',
            'idiomSuggestions'
        ));
    }
}
