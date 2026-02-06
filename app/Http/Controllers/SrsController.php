<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\UserVocabProgress;
use App\Models\LearningLog;

class SrsController extends Controller
{
    /**
     * 📘 TRẢ LỜI KHI HỌC TỪ MỚI
     */
    public function answer(Request $request)
    {
        $request->validate([
            'vocabulary_id' => 'required|exists:vocabularies,id',
            'topic_id' => 'required|exists:topics,id',
            'index' => 'required|integer',
            'result' => 'required|in:correct,wrong',
        ]);

        $progress = UserVocabProgress::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'vocabulary_id' => $request->vocabulary_id,
            ],
            [
                'repetition' => 0,
                'interval' => 1,
                'next_review_at' => now(),
            ]
        );

        // 📌 GHI LOG HỌC
        LearningLog::create([
            'user_id' => Auth::id(),
            'vocabulary_id' => $request->vocabulary_id,
            'action' => 'learn',
            'result' => $request->result,
            'interval' => $progress->interval,
        ]);

        if ($request->result === 'correct') {
            $progress->repetition++;

            $progress->interval = match ($progress->repetition) {
                1 => 1,
                2 => 3,
                3 => 7,
                default => 14,
            };

            $progress->next_review_at = now()->addDays($progress->interval);
        } else {
            // ❌ Chưa nhớ → ép ôn ngay
            $progress->repetition = 0;
            $progress->interval = 1;
            $progress->next_review_at = now();
        }

        $progress->save();

        return redirect(
            '/topics/' . $request->topic_id . '/flashcard?index=' . ($request->index + 1)
        );
    }

    /**
     * 📚 DANH SÁCH TỪ CẦN ÔN
     */
    public function review()
    {
        $reviews = UserVocabProgress::with('vocabulary')
            ->where('user_id', auth()->id())
            ->where(function ($q) {
                $q->where('repetition', 0)
                    ->orWhere('next_review_at', '<=', now());
            })
            ->orderBy('next_review_at')
            ->get();

        return view('srs.review', compact('reviews'));
    }

    /**
     * 🃏 FLASHCARD ÔN
     */
    public function reviewCard(UserVocabProgress $progress)
    {
        abort_if($progress->user_id !== auth()->id(), 403);

        return view('srs.flashcard', [
            'progress' => $progress,
            'vocabulary' => $progress->vocabulary,
        ]);
    }

    /**
     * ✅ / ❌ TRẢ LỜI ÔN TẬP
     */
    public function reviewAnswer(Request $request)
    {
        $request->validate([
            'progress_id' => 'required|exists:user_vocab_progress,id',
            'result' => 'required|in:correct,wrong',
        ]);

        $current = UserVocabProgress::findOrFail($request->progress_id);
        abort_if($current->user_id !== auth()->id(), 403);

        // 📌 LOG
        LearningLog::create([
            'user_id' => auth()->id(),
            'vocabulary_id' => $current->vocabulary_id,
            'action' => 'review',
            'result' => $request->result,
            'interval' => $current->interval,
        ]);

        if ($request->result === 'wrong') {
            // ❌ CHƯA NHỚ → LƯU LẠI THỜI GIAN MỚI
            $current->repetition = 0;
            $current->interval = 1;
            $current->next_review_at = now()->addMinutes(10);
            $current->save();
        } else {
            // ✅ BIẾT RỒI → XÓA
            $current->delete();
        }

        // 👉 TÌM TỪ KHÁC (KHÔNG LẤY LẠI TỪ HIỆN TẠI)
        $next = UserVocabProgress::with('vocabulary')
            ->where('user_id', auth()->id())
            ->where('id', '!=', $current->id)
            ->where(function ($q) {
                $q->where('repetition', 0)
                    ->orWhere('next_review_at', '<=', now());
            })
            ->orderBy('next_review_at')
            ->first();

        if ($next) {
            return redirect()->route('srs.card', $next->id);
        }

        // 👉 KHÔNG CÒN TỪ
        return redirect()
            ->route('srs.review')
            ->with('success', '🎉 Bạn đã hoàn thành lượt ôn tập!');
    }

    /**
     * ⏭️ TỪ ÔN TIẾP THEO
     */
    public function nextReview()
    {
        $progress = UserVocabProgress::with('vocabulary')
            ->where('user_id', auth()->id())
            ->where(function ($q) {
                $q->where('repetition', 0)
                    ->orWhere('next_review_at', '<=', now());
            })
            ->orderBy('next_review_at')
            ->first();

        if (!$progress) {
            return redirect()->route('srs.review');
        }

        return redirect()->route('srs.card', $progress->id);
    }
}
