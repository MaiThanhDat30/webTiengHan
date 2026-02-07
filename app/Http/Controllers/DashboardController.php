<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\LearningLog;
use App\Models\UserVocabProgress;
use App\Models\Idiom;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | 1️⃣ THỐNG KÊ CƠ BẢN
        |--------------------------------------------------------------------------
        */

        // Tổng số từ đã từng học
        $totalLearned = LearningLog::where('user_id', $userId)
            ->where('action', 'learn')
            ->distinct('vocabulary_id')
            ->count('vocabulary_id');

        // Số từ đến hạn ôn
        $needReview = UserVocabProgress::where('user_id', $userId)
            ->where('next_review_at', '<=', now())
            ->count();

        // Số hoạt động hôm nay
        $todayActivity = LearningLog::where('user_id', $userId)
            ->whereDate('created_at', today())
            ->count();

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ ĐÚNG / SAI HÔM NAY → ĐÁNH GIÁ TRÌNH ĐỘ
        |--------------------------------------------------------------------------
        */

        $todayCorrect = LearningLog::where('user_id', $userId)
            ->whereDate('created_at', today())
            ->where('result', 'correct')
            ->count();

        $todayWrong = LearningLog::where('user_id', $userId)
            ->whereDate('created_at', today())
            ->where('result', 'wrong')
            ->count();


        $totalReviews = $todayCorrect + $todayWrong;

        $accuracy = $totalReviews > 0
            ? round(($todayCorrect / $totalReviews) * 100)
            : 0;

        // Đánh giá trình độ (đơn giản – có thể nâng cấp sau)
        $level = match (true) {
            $accuracy < 50 => 'Yếu',
            $accuracy < 70 => 'Trung bình',
            $accuracy < 85 => 'Khá',
            default => 'Tốt',
        };

        /*
        |--------------------------------------------------------------------------
        | 3️⃣ BIỂU ĐỒ 7 NGÀY GẦN NHẤT
        |--------------------------------------------------------------------------
        */

        $last7Days = LearningLog::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 4️⃣ TỪ VỰNG CÓ VẤN ĐỀ (HAY SAI / HAY QUÊN)
        |--------------------------------------------------------------------------
        */

        $problemVocabs = LearningLog::join(
            'vocabularies',
            'learning_logs.vocabulary_id',
            '=',
            'vocabularies.id'
        )
            ->select(
                'learning_logs.vocabulary_id',
                'vocabularies.word_kr',
                DB::raw("SUM(CASE WHEN learning_logs.result = 'wrong' THEN 1 ELSE 0 END) as wrongs"),
                DB::raw('COUNT(*) as total')
            )
            ->where('learning_logs.user_id', $userId)
            ->groupBy('learning_logs.vocabulary_id', 'vocabularies.word_kr')
            ->having('wrongs', '>=', 2)
            ->orderByDesc('wrongs')
            ->limit(10)
            ->get()
            ->map(function ($item) use ($userId) {
                $progress = UserVocabProgress::where('user_id', $userId)
                    ->where('vocabulary_id', $item->vocabulary_id)
                    ->first();

                $item->tag = ($progress && $progress->next_review_at <= now())
                    ? 'Hay quên'
                    : 'Hay sai';

                return $item;
            });

        /*
        |--------------------------------------------------------------------------
        | 5️⃣ TỪ ĐẾN HẠN ÔN (SRS)
        |--------------------------------------------------------------------------
        */

        $dueVocabs = UserVocabProgress::where('user_id', $userId)
            ->where('next_review_at', '<=', now())
            ->orderBy('next_review_at')
            ->limit(10)
            ->with('vocabulary') // relation vocab
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 6️⃣ GỢI Ý LỘ TRÌNH HỌC (RULE-BASED)
        |--------------------------------------------------------------------------
        */

        $suggestion = match (true) {
            $needReview >= 20 =>
            'Bạn đang có nhiều từ đến hạn ôn. Nên ưu tiên ôn tập trước khi học từ mới.',
            $accuracy < 60 =>
            'Độ chính xác còn thấp. Nên giảm tốc độ học từ mới và tăng số lần ôn.',
            $totalLearned < 100 =>
            'Bạn đang ở giai đoạn nền tảng. Mỗi ngày học 10–15 từ là phù hợp.',
            default =>
            'Tiến độ tốt! Có thể tiếp tục học từ mới và duy trì ôn tập đều đặn.',
        };
        /*
        |--------------------------------------------------------------------------
        | 8️⃣ TỪ KHÓ / HAY QUÊN TOÀN HỆ THỐNG
        |--------------------------------------------------------------------------
        */

        $globalHardVocabs = LearningLog::join(
            'vocabularies',
            'learning_logs.vocabulary_id',
            '=',
            'vocabularies.id'
        )
            ->select(
                'learning_logs.vocabulary_id',
                'vocabularies.word_kr',
                DB::raw("SUM(CASE WHEN learning_logs.result = 'wrong' THEN 1 ELSE 0 END) as wrongs"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('learning_logs.vocabulary_id', 'vocabularies.word_kr')
            ->having('wrongs', '>=', 10) // ngưỡng "khó"
            ->orderByDesc('wrongs')
            ->limit(10)
            ->get();
        // 8️⃣ BXH TỪ VỰNG BỊ SAI NHIỀU NHẤT (TOÀN HỆ THỐNG)
        $globalWrongRanking = LearningLog::join(
            'vocabularies',
            'learning_logs.vocabulary_id',
            '=',
            'vocabularies.id'
        )
            ->where('learning_logs.result', 'wrong')
            ->select(
                'vocabularies.word_kr',
                DB::raw('COUNT(*) as wrong_times')
            )
            ->groupBy('vocabularies.word_kr')
            ->orderByDesc('wrong_times')
            ->limit(5)
            ->get();


        /*

|--------------------------------------------------------------------------
| 9️⃣ CÂU QUÁN DỤNG NGỮ / MẪU CÂU HAY
|--------------------------------------------------------------------------
*/

        $idiomSuggestions = Idiom::inRandomOrder()
            ->limit(5)
            ->get();
        /*

        |--------------------------------------------------------------------------
        | 7️⃣ TRẢ VIEW
        |--------------------------------------------------------------------------
        */

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
            'globalHardVocabs',
            'globalWrongRanking',
            'idiomSuggestions'
        ));

    }
}
