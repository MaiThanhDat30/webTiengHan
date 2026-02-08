<?php

namespace App\Services;

use App\Models\UserVocabProgress;
use App\Models\LearningLog;

class SrsService
{
    // 1 → 3 → 7 → 14 → 30 ngày
    public static array $intervals = [1, 3, 7, 14, 30];

    public static function answer(
        int $userId,
        int $vocabularyId,
        string $result,
        string $action = 'review'
    ): void {
        /*
        |--------------------------------------------------------------------------
        | 1️⃣ LOG REVIEW (luôn log)
        |--------------------------------------------------------------------------
        */
        LearningLog::create([
            'user_id'       => $userId,
            'vocabulary_id' => $vocabularyId,
            'action'        => 'review',
            'result'        => $result,
        ]);

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ XỬ LÝ SRS
        |--------------------------------------------------------------------------
        */
        $progress = UserVocabProgress::firstOrCreate(
            [
                'user_id'       => $userId,
                'vocabulary_id' => $vocabularyId,
            ],
            [
                'step'           => 0,
                'next_review_at' => now(),
            ]
        );

        if ($result === 'correct') {
            self::handleCorrect($progress);

            /*
            |--------------------------------------------------------------------------
            | 3️⃣ ĐÁNH DẤU "ĐÃ HỌC" (CHỈ 1 LẦN DUY NHẤT)
            |--------------------------------------------------------------------------
            */
            $alreadyLearned = LearningLog::where('user_id', $userId)
                ->where('vocabulary_id', $vocabularyId)
                ->where('action', 'learn')
                ->exists();

            if (!$alreadyLearned) {
                LearningLog::create([
                    'user_id'       => $userId,
                    'vocabulary_id' => $vocabularyId,
                    'action'        => 'learn',
                ]);
            }
        } else {
            self::handleWrong($progress);
        }
    }

    /* =========================
       ✅ BIẾT
    ========================= */
    private static function handleCorrect(UserVocabProgress $progress): void
    {
        $nextStep = min(
            $progress->step + 1,
            count(self::$intervals) - 1
        );

        $progress->update([
            'step'           => $nextStep,
            'next_review_at' => now()->addDays(self::$intervals[$nextStep]),
        ]);
    }

    /* =========================
       ❌ QUÊN
    ========================= */
    private static function handleWrong(UserVocabProgress $progress): void
    {
        $progress->update([
            'step'           => 0,
            'next_review_at' => now()->addDay(),
        ]);
    }
}
