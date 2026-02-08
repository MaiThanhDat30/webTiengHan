<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserVocabProgress;
use App\Models\LearningLog;
use Illuminate\Http\Response;
use App\Services\SrsService;
class SrsController extends Controller
{
    /* ============================
       📘 HỌC TỪ MỚI (FLASHCARD)
    ============================ */

    public function answer(Request $request)
    {
        $data = $request->validate([
            'vocabulary_id' => 'required|exists:vocabularies,id',
            'result' => 'required|in:correct,wrong',
        ]);

        SrsService::answer(
            auth()->id(),
            $data['vocabulary_id'],
            $data['result'],
            'learn'
        );

        return response()->noContent(); // 204
    }

    /* ============================
       📚 DANH SÁCH ÔN
    ============================ */

    public function review()
    {
        $reviews = $this->dueReviews()
            ->orderBy('next_review_at')
            ->get();

        // 👉 tạo thứ tự tạm thời
        $order = $reviews->pluck('id')->values()->toArray();

        session([
            'srs_review_order' => $order,
        ]);

        return view('srs.review', compact('reviews'));
    }

    /* ============================
       🃏 FLASHCARD ÔN
    ============================ */

    public function reviewCard(UserVocabProgress $progress)
    {// 🔥 NẾU PROGRESS ĐÃ BỊ XOÁ → QUAY VỀ DANH SÁCH ÔN
        if (!$progress->exists) {
            return redirect()->route('srs.review');
        }
        $this->authorizeProgress($progress);

        // 🔥 PRELOAD TOÀN BỘ THẺ ÔN (1 QUERY)
        $cards = $this->dueReviews()
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'word_kr' => $p->vocabulary->word_kr,
                'word_vi' => $p->vocabulary->word_vi,
            ])
            ->values();

        return view('srs.flashcard', [
            'cards' => $cards,
        ]);
    }
    public function reviewJson(UserVocabProgress $progress)
    {
        if (!$progress->exists) {
            return response()->json(null, 204);
        }

        $this->authorizeProgress($progress);

        return response()->json([
            'id' => $progress->id,
            'word_kr' => $progress->vocabulary->word_kr,
            'word_vi' => $progress->vocabulary->word_vi,
        ]);
    }
    /* ============================
       ✅ / ❌ KHI ÔN
    ============================ */

    public function reviewAnswer(Request $request)
    {
        $data = $request->validate([
            'progress_id' => 'required|exists:user_vocab_progress,id',
            'result' => 'required|in:correct,wrong',
        ]);
    
        $progress = UserVocabProgress::findOrFail($data['progress_id']);
        $this->authorizeProgress($progress);
    
        // 🔁 XỬ LÝ SRS
        SrsService::answer(
            auth()->id(),
            $progress->vocabulary_id,
            $data['result'],
            'review'
        );
    
        if ($data['result'] === 'correct') {
            // ✅ BIẾT → XOÁ KHỎI DB
            $progress->delete();
        } else {
            // ❌ CHƯA NHỚ → RESET
            $progress->update([
                'step' => 0,
                'next_review_at' => now(),
            ]);
        }
    
        /* ===============================
           🔥 FIX LỖI TỪ CUỐI VẪN HIỆN
        =============================== */
    
        $order = session('srs_review_order', []);
    
        // ❌ XÓA ID ĐÃ TRẢ LỜI KHỎI SESSION
        $order = array_values(
            array_filter($order, fn ($id) => $id != $progress->id)
        );
    
        if (empty($order)) {
            // ✅ HẾT ÔN → CLEAR SESSION
            session()->forget('srs_review_order');
        } else {
            session(['srs_review_order' => $order]);
        }
    
        return response()->noContent(); // 204
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

        $userId = auth()->id();

        $exists = UserVocabProgress::where([
            'user_id' => $userId,
            'vocabulary_id' => $data['vocabulary_id'],
        ])->exists();

        if ($exists) {
            UserVocabProgress::where([
                'user_id' => $userId,
                'vocabulary_id' => $data['vocabulary_id'],
            ])->delete();

            return back()->with('unsaved', true);
        }

        UserVocabProgress::create([
            'user_id' => $userId,
            'vocabulary_id' => $data['vocabulary_id'],
            'step' => 0,
            'next_review_at' => now(),
        ]);

        return back()->with('saved', true);
    }

    /* ============================
       🧠 HELPERS
    ============================ */

    private function dueReviews()
    {
        return UserVocabProgress::with('vocabulary')
            ->where('user_id', auth()->id())
            ->where('next_review_at', '<=', now())
            ->orderBy('next_review_at');
    }

    private function authorizeProgress(UserVocabProgress $progress): void
    {
        abort_if($progress->user_id !== auth()->id(), 403);
    }
}
