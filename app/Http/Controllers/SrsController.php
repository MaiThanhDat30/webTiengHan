<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserVocabProgress;
use App\Models\LearningLog;

class SrsController extends Controller
{
    /* ============================
       📘 HỌC TỪ MỚI
    ============================ */

    public function answer(Request $request)
    {
        $data = $request->validate([
            'vocabulary_id' => 'required|exists:vocabularies,id',
            'topic_id' => 'required|exists:topics,id',
            'index' => 'required|integer',
            'result' => 'required|in:correct,wrong',
        ]);

        // Ghi log học
        $this->logLearning(
            $data['vocabulary_id'],
            'learn',
            $data['result']
        );

        if ($data['result'] === 'wrong') {
            // ❌ CHƯA THUỘC → LƯU VÀO ÔN TẬP (NẾU CHƯA CÓ)
            UserVocabProgress::firstOrCreate(
                [
                    'user_id' => auth()->id(),
                    'vocabulary_id' => $data['vocabulary_id'],
                ],
                [
                    'step' => 0,
                    'next_review_at' => now(),
                ]
            );
        } else {
            // ✅ ĐÃ THUỘC → XOÁ KHỎI ÔN (NẾU TỪNG LƯU)
            UserVocabProgress::where('user_id', auth()->id())
                ->where('vocabulary_id', $data['vocabulary_id'])
                ->delete();
        }

        // 👉 LUÔN SANG TỪ MỚI
        return redirect(
            route('topics.flashcard', $data['topic_id']) .
            '?index=' . ($data['index'] + 1)
        );
    }
    /* ============================
       📚 DANH SÁCH ÔN
    ============================ */

    public function review()
    {
        $reviews = $this->dueReviews()->get();
        return view('srs.review', compact('reviews'));
    }

    /* ============================
       🃏 FLASHCARD ÔN
    ============================ */

    public function reviewCard(UserVocabProgress $progress)
    {
        $this->authorizeProgress($progress);

        return view('srs.flashcard', [
            'progress' => $progress,
            'vocabulary' => $progress->vocabulary,
        ]);
    }

    /* ============================
       ✅ / ❌ TRẢ LỜI KHI ÔN
    ============================ */

    public function reviewAnswer(Request $request)
{
    $data = $request->validate([
        'progress_id' => 'required|exists:user_vocab_progress,id',
        'result' => 'required|in:correct,wrong',
    ]);

    $progress = UserVocabProgress::findOrFail($data['progress_id']);
    $this->authorizeProgress($progress);

    $this->logLearning(
        $progress->vocabulary_id,
        'review',
        $data['result']
    );

    if ($data['result'] === 'correct') {
        // ✅ BIẾT RỒI → XOÁ KHỎI ÔN
        $progress->delete();

        $next = $this->dueReviews()->first();

        return $next
            ? redirect()->route('srs.card', $next->id)
            : redirect()->route('srs.review')
                ->with('success', '🎉 Bạn đã hoàn thành lượt ôn hôm nay!');
    }

    // ❌ CHƯA NHỚ → GIỮ LẠI
    $this->resetProgress($progress);

    // 👉 TÌM TỪ KHÁC (KHÔNG PHẢI CHÍNH NÓ)
    $next = $this->dueReviews()
        ->where('id', '!=', $progress->id)
        ->first();

    // 👉 NẾU CÒN TỪ KHÁC → SANG TỪ ĐÓ
    if ($next) {
        return redirect()->route('srs.card', $next->id);
    }

    // 👉 NẾU ĐÂY LÀ TỪ CUỐI → QUAY VỀ DANH SÁCH ÔN
    return redirect()->route('srs.review')
        ->with('info', '📌 Từ này đã được giữ lại để ôn sau');
}

    /* ============================
       ⏭️ LẤY TỪ ÔN TIẾP
    ============================ */

    public function nextReview()
    {
        $progress = $this->dueReviews()->first();

        return $progress
            ? redirect()->route('srs.card', $progress->id)
            : redirect()->route('srs.review');
    }

    /* ============================
       📌 LƯU / HUỶ LƯU ÔN
    ============================ */

    public function toggle(Request $request)
    {
        $data = $request->validate([
            'vocabulary_id' => 'required|exists:vocabularies,id',
        ]);

        $progress = UserVocabProgress::where('user_id', auth()->id())
            ->where('vocabulary_id', $data['vocabulary_id'])
            ->first();

        if ($progress) {
            // ❌ Đã lưu → huỷ lưu
            $progress->delete();
            return back()->with('unsaved', true);
        }

        // ✅ Chưa lưu → lưu
        UserVocabProgress::create([
            'user_id' => auth()->id(),
            'vocabulary_id' => $data['vocabulary_id'],
            'step' => 0,
            'next_review_at' => now(),
        ]);

        return back()->with('saved', true);
    }

    /* ============================
       🧠 HELPERS
    ============================ */

    private function getOrCreateProgress(int $vocabularyId): UserVocabProgress
    {
        return UserVocabProgress::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'vocabulary_id' => $vocabularyId,
            ],
            [
                'step' => 0,
                'next_review_at' => now(),
            ]
        );
    }

    private function dueReviews()
    {
        return UserVocabProgress::with('vocabulary')
            ->where('user_id', auth()->id())
            ->where('next_review_at', '<=', now())
            ->orderBy('next_review_at');
    }

    private function resetProgress(UserVocabProgress $progress): void
    {
        $progress->update([
            'step' => 0,
            'next_review_at' => now(), // ✅ VẪN CÒN TRONG DANH SÁCH ÔN
        ]);
    }

    private function authorizeProgress(UserVocabProgress $progress): void
    {
        abort_if($progress->user_id !== auth()->id(), 403);
    }

    private function logLearning(
        int $vocabularyId,
        string $action,
        string $result
    ): void {
        LearningLog::create([
            'user_id' => auth()->id(),
            'vocabulary_id' => $vocabularyId,
            'action' => $action,
            'result' => $result,
        ]);
    }
}
