<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                'step' => 0,
                'next_review_at' => now()->addDay(),
            ]
        );

        // 📌 LOG
        LearningLog::create([
            'user_id' => Auth::id(),
            'vocabulary_id' => $request->vocabulary_id,
            'action' => 'learn',
            'result' => $request->result,
            'interval' => null,
        ]);

        if ($request->result === 'correct') {
            $this->moveToNextStep($progress);
        } else {
            $this->resetProgress($progress);
        }

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
            ->where('next_review_at', '<=', now())
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

        $progress = UserVocabProgress::findOrFail($request->progress_id);
        abort_if($progress->user_id !== auth()->id(), 403);

        // 📌 LOG
        LearningLog::create([
            'user_id' => auth()->id(),
            'vocabulary_id' => $progress->vocabulary_id,
            'action' => 'review',
            'result' => $request->result,
            'interval' => null,
        ]);

        if ($request->result === 'correct') {
            $this->moveToNextStep($progress);
        } else {
            $this->resetProgress($progress);
        }

        // 👉 LẤY TỪ TIẾP THEO
        $next = UserVocabProgress::with('vocabulary')
            ->where('user_id', auth()->id())
            ->where('next_review_at', '<=', now())
            ->orderBy('next_review_at')
            ->first();

        if ($next) {
            return redirect()->route('srs.card', $next->id);
        }

        return redirect()
            ->route('srs.review')
            ->with('success', '🎉 Bạn đã hoàn thành lượt ôn hôm nay!');
    }

    /**
     * ⏭️ TỪ ÔN TIẾP THEO
     */
    public function nextReview()
    {
        $progress = UserVocabProgress::with('vocabulary')
            ->where('user_id', auth()->id())
            ->where('next_review_at', '<=', now())
            ->orderBy('next_review_at')
            ->first();

        if (!$progress) {
            return redirect()->route('srs.review');
        }

        return redirect()->route('srs.card', $progress->id);
    }

    /* =====================================================
       🔁 SRS LOGIC (1–3–7–14–30)
    ===================================================== */

    private function moveToNextStep(UserVocabProgress $progress)
    {
        $steps = [1, 3, 7, 14, 30];

        $currentStep = min($progress->step, count($steps) - 1);
        $days = $steps[$currentStep];

        $progress->step = $currentStep + 1;
        $progress->next_review_at = now()->addDays($days);
        $progress->save();
    }

    private function resetProgress(UserVocabProgress $progress)
    {
        $progress->step = 0;
        $progress->next_review_at = now()->addDay();
        $progress->save();
    }
}
