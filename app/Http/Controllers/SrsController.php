<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\UserVocabProgress;
use App\Models\LearningLog;
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
            'result'        => 'required|in:correct,wrong',
        ]);

        $userId = auth()->id();

        // ✅ GIAO TOÀN BỘ CHO SRS SERVICE
        SrsService::answer(
            $userId,
            $data['vocabulary_id'],
            $data['result'],
            'learn'
        );

        // 🔥 CLEAR CACHE DASHBOARD
        Cache::forget("dashboard_v4_user_{$userId}_" . now()->toDateString());

        return response()->noContent();
    }

    /* ============================
       📚 DANH SÁCH ÔN
    ============================ */
    public function review()
    {
        $reviews = $this->dueReviews()
            ->orderBy('next_review_at')
            ->get();

        session([
            'srs_review_order' => $reviews->pluck('id')->values()->toArray(),
        ]);

        return view('srs.review', compact('reviews'));
    }

    /* ============================
       🃏 FLASHCARD ÔN
    ============================ */
    public function reviewCard(UserVocabProgress $progress)
    {
        if (!$progress->exists) {
            return redirect()->route('srs.review');
        }

        $this->authorizeProgress($progress);

        $cards = $this->dueReviews()
            ->get()
            ->map(fn($p) => [
                'id'      => $p->id,
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
            'id'      => $progress->id,
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
            'result'      => 'required|in:correct,wrong',
        ]);

        $progress = UserVocabProgress::findOrFail($data['progress_id']);
        $this->authorizeProgress($progress);

        // 🔁 GỌI SERVICE (TỰ LO LOG + SRS + LEARN 1 LẦN)
        SrsService::answer(
            auth()->id(),
            $progress->vocabulary_id,
            $data['result'],
            'review'
        );

        // 🔥 CLEAR CACHE DASHBOARD
        Cache::forget("dashboard_v4_user_" . auth()->id() . "_" . now()->toDateString());

        // 🔥 FIX: remove khỏi session order
        $order = session('srs_review_order', []);
        $order = array_values(array_filter(
            $order,
            fn($id) => $id != $progress->id
        ));

        empty($order)
            ? session()->forget('srs_review_order')
            : session(['srs_review_order' => $order]);

        return response()->noContent();
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
            'user_id'       => $userId,
            'vocabulary_id' => $data['vocabulary_id'],
        ])->exists();

        if ($exists) {
            UserVocabProgress::where([
                'user_id'       => $userId,
                'vocabulary_id' => $data['vocabulary_id'],
            ])->delete();

            return back()->with('unsaved', true);
        }

        UserVocabProgress::create([
            'user_id'       => $userId,
            'vocabulary_id' => $data['vocabulary_id'],
            'step'          => 0,
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
