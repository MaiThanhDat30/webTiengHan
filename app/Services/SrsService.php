<?php

namespace App\Services;

use App\Models\UserVocabProgress;
use Carbon\Carbon;
use App\Models\LearningLog;

class SrsService
{
    // Mốc ôn tập: 1 → 3 → 7 → 14 → 30 ngày
    public static array $intervals = [1, 3, 7, 14, 30];
    public static function answer(
        int $userId,
        int $vocabularyId,
        string $result,
        string $action = 'learn' // learn | review
    ): void {
        // ✅ GHI LOG (nhẹ)
        LearningLog::create([
            'user_id' => $userId,
            'vocabulary_id' => $vocabularyId,
            'action' => $action,
            'result' => $result,
        ]);

        if ($result === 'wrong') {
            // ❌ CHƯA NHỚ → đưa vào ôn tập
            UserVocabProgress::updateOrCreate(
                [
                    'user_id' => $userId,
                    'vocabulary_id' => $vocabularyId,
                ],
                [
                    'step' => 0,
                    'next_review_at' => now(),
                ]
            );
        } else {
            // ✅ BIẾT → xoá khỏi ôn tập (nếu có)
            UserVocabProgress::where([
                'user_id' => $userId,
                'vocabulary_id' => $vocabularyId,
            ])->delete();
        }
    }
    public static function createProgress(int $userId, int $vocabId): void
    {
        UserVocabProgress::firstOrCreate(
            [
                'user_id' => $userId,
                'vocabulary_id' => $vocabId,
            ],
            [
                'step' => 0,
                'next_review_at' => Carbon::now()->addDay(),
            ]
        );
    }

    public static function correct(UserVocabProgress $progress): void
    {
        $nextStep = min(
            $progress->step + 1,
            count(self::$intervals) - 1
        );

        $progress->update([
            'step' => $nextStep,
            'next_review_at' => Carbon::now()->addDays(self::$intervals[$nextStep]),
        ]);
    }

    public static function wrong(UserVocabProgress $progress): void
    {
        $progress->update([
            'step' => 0,
            'next_review_at' => Carbon::now()->addDay(),
        ]);
    }
}
