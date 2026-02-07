<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

use App\Models\LearningLog;
use App\Models\UserVocabProgress;
use App\Models\Idiom;
use App\Mail\DailyReviewReminderMail;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;
        $today = today();

        /* =====================================================
         | 1️⃣ THỐNG KÊ CƠ BẢN (tối ưu query)
         ===================================================== */

        $totalLearned = LearningLog::where('user_id', $userId)
            ->where('action', 'learn')
            ->distinct('vocabulary_id')
            ->count('vocabulary_id');

        $needReview = UserVocabProgress::where('user_id', $userId)
            ->where('next_review_at', '<=', now())
            ->count();

        $todayActivity = LearningLog::where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->count();

        /* =====================================================
         | 2️⃣ ĐÚNG / SAI HÔM NAY (FIX POSTGRES)
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
         | 3️⃣ TỪ ĐẾN HẠN ÔN (SRS)
         ===================================================== */

        $dueVocabs = UserVocabProgress::with('vocabulary')
            ->where('user_id', $userId)
            ->where('next_review_at', '<=', now())
            ->orderBy('next_review_at')
            ->limit(10)
            ->get();

        /* =====================================================
         | 4️⃣ MAIL NHẮC ÔN (KHÔNG LÀM CHẬM DASHBOARD)
         ===================================================== */

        if ($dueVocabs->isNotEmpty()) {
            $alreadySentToday = DB::table('review_notifications')
                ->where('user_id', $userId)
                ->where('sent_date', $today)
                ->exists();

            if (! $alreadySentToday) {
                try {
                    Mail::to(
                        app()->isLocal()
                            ? 'callmedat999@gmail.com'
                            : $user->email
                    )->queue(new DailyReviewReminderMail($user, $dueVocabs));

                    DB::table('review_notifications')->insert([
                        'user_id'    => $userId,
                        'sent_date'  => $today,
                        'created_at'=> now(),
                        'updated_at'=> now(),
                    ]);
                } catch (\Throwable $e) {
                    logger()->error('Mail error: ' . $e->getMessage());
                }
            }
        }

        /* =====================================================
         | 5️⃣ BIỂU ĐỒ 7 NGÀY (POSTGRES SAFE)
         ===================================================== */

        $last7Days = LearningLog::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(6))
            ->selectRaw("DATE(created_at) AS date, COUNT(*) AS total")
            ->groupByRaw("DATE(created_at)")
            ->orderBy('date')
            ->get();

        /* =====================================================
         | 6️⃣ TỪ HAY SAI / HAY QUÊN (giảm N+1)
         ===================================================== */

        $problemVocabs = LearningLog::join(
                'vocabularies',
                'learning_logs.vocabulary_id',
                '=',
                'vocabularies.id'
            )
            ->leftJoin('user_vocab_progress', function ($join) use ($userId) {
                $join->on('learning_logs.vocabulary_id', '=', 'user_vocab_progress.vocabulary_id')
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
                learning_logs.vocabulary_id,
                vocabularies.word_kr,
                SUM(CASE WHEN learning_logs.result = 'wrong' THEN 1 ELSE 0 END) AS wrongs,
                CASE
                    WHEN user_vocab_progress.next_review_at <= NOW()
                    THEN 'Hay quên'
                    ELSE 'Hay sai'
                END AS tag
            ")
            ->orderByDesc('wrongs')
            ->limit(10)
            ->get();

        /* =====================================================
         | 7️⃣ GỢI Ý LỘ TRÌNH
         ===================================================== */

        $suggestion = match (true) {
            $needReview >= 20 =>
                'Bạn đang có nhiều từ đến hạn ôn. Nên ưu tiên ôn tập trước khi học từ mới.',
            $accuracy < 60 =>
                'Độ chính xác còn thấp. Nên giảm tốc độ học từ mới và tăng số lần ôn.',
            $totalLearned < 100 =>
                'Bạn đang ở giai đoạn nền tảng. Mỗi ngày học 10–15 từ là phù hợp.',
            default =>
                'Tiến độ tốt! Tiếp tục duy trì đều đặn.',
        };

        /* =====================================================
         | 8️⃣ BXH TỪ KHÓ (TOÀN HỆ THỐNG)
         ===================================================== */

        $globalWrongRanking = LearningLog::join(
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

        /* =====================================================
         | 9️⃣ IDIOM
         ===================================================== */

        $idiomSuggestions = Idiom::inRandomOrder()
            ->limit(5)
            ->get();

        /* =====================================================
         | 🔟 VIEW
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
            'dueVocabs',
            'suggestion',
            'globalWrongRanking',
            'idiomSuggestions'
        ));
    }
}
