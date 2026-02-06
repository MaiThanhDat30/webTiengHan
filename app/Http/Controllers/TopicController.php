<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Topic;

class TopicController extends Controller
{
    /**
     * 1️⃣ Danh sách chủ đề CHA
     * VD: TOPIK, Gia đình, Khoa học...
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
     * - Có con → hiển thị danh sách topic con
     * - Không có con → hiển thị từ vựng
     */
    public function show($id)
{
    // ❌ KHÔNG load vocabularies ở đây
    $topic = Topic::with('children')->findOrFail($id);

    // ✅ Chỉ phân trang khi KHÔNG có topic con
    $vocabularies = $topic->children->count() === 0
        ? $topic->vocabularies()->paginate(10)
        : collect();

    return view('topics.show', compact(
        'topic',
        'vocabularies'
    ));
}

    /**
     * 🔥 FLASHCARD – chỉ dùng cho topic CON (có vocab)
     */
    public function flashcard(Request $request, $id)
    {
        $topic = Topic::with('vocabularies')->findOrFail($id);

        // ❌ Topic không có từ vựng thì không cho flashcard
        if ($topic->vocabularies->isEmpty()) {
            abort(404, 'Topic này không có từ vựng');
        }

        $index = (int) $request->query('index', 0);
        $total = $topic->vocabularies->count();

        // ✅ Hết từ → màn hoàn thành
        if ($index >= $total) {
            return view('topics.flashcard-finish', compact('topic'));
        }

        if ($index < 0)
            $index = 0;

        $vocabulary = $topic->vocabularies[$index];

        return view('topics.flashcard', compact(
            'topic',
            'vocabulary',
            'index',
            'total'
        ));
    }
}
