<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Topic;
use App\Models\UserVocabProgress;

class TopicController extends Controller
{
    /**
     * 1️⃣ Danh sách chủ đề CHA
     */
    public function index()
    {
        $topics = Topic::whereNull('parent_id')
            ->with('children')
            ->get();

        return view('topics.index', compact('topics'));
    }

    /**
     * 2️⃣ Xem chi tiết 1 topic
     * - Có con → hiển thị topic con
     * - Không có con → hiển thị từ vựng
     */
    public function show($id)
    {
        // Load topic + children
        $topic = Topic::with('children')->findOrFail($id);

        // Nếu KHÔNG có topic con → load vocab
        $vocabularies = $topic->children->count() === 0
            ? $topic->vocabularies()->paginate(10)
            : collect();

        // ✅ LẤY TỪ ĐÃ LƯU ÔN (ĐÚNG BẢNG)
        $reviewedIds = UserVocabProgress::where('user_id', auth()->id())
            ->pluck('vocabulary_id')
            ->toArray();

        return view('topics.show', compact(
            'topic',
            'vocabularies',
            'reviewedIds'
        ));
    }

    /**
     * 🔥 FLASHCARD – chỉ dùng cho topic CON
     */
    public function flashcard(Request $request, $id)
    {
        $topic = Topic::with('vocabularies')->findOrFail($id);

        if ($topic->vocabularies->isEmpty()) {
            abort(404, 'Topic này không có từ vựng');
        }

        $index = (int) $request->query('index', 0);
        $total = $topic->vocabularies->count();

        if ($index >= $total) {
            return view('topics.flashcard-finish', compact('topic'));
        }

        if ($index < 0) {
            $index = 0;
        }

        $vocabulary = $topic->vocabularies[$index];

        return view('topics.flashcard', compact(
            'topic',
            'vocabulary',
            'index',
            'total'
        ));
    }
}
