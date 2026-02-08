<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Topic;
use App\Models\UserVocabProgress;
use App\Models\Vocabulary;
use Illuminate\Http\JsonResponse;
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
    public function preloadFlashcards(Request $request, $id): JsonResponse
    {
        $start = max((int) $request->query('start', 0), 0);
        $limit = 3;

        $vocabIds = $this->getCachedVocabIds($id);

        $slice = array_slice($vocabIds, $start, $limit);

        if (empty($slice)) {
            return response()->json([]);
        }

        $vocabs = Vocabulary::whereIn('id', $slice)
            ->get(['id', 'word_kr', 'word_vi'])
            ->sortBy(fn($v) => array_search($v->id, $slice))
            ->values();

        return response()->json($vocabs);
    }
    /**
     * 2️⃣ Xem chi tiết 1 topic
     * - Có con → hiển thị topic con
     * - Không có con → hiển thị từ vựng
     */
    public function show($id)
    {
        $topic = Topic::with('children')->findOrFail($id);

        // Nếu topic không có con → load vocab
        $vocabularies = $topic->children->isEmpty()
            ? $topic->vocabularies()->paginate(10)
            : collect();

        // Từ đã lưu ôn
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
     * 🔥 FLASHCARD – CỰC NHANH (KHÔNG OFFSET)
     */
    public function flashcard(Request $request, $id)
    {
        $topic = Topic::findOrFail($id);

        // index hiện tại
        $index = max((int) $request->query('index', 0), 0);

        /**
         * ✅ CACHE DANH SÁCH ID VOCAB (NHẸ + NHANH)
         */
        $vocabIds = $this->getCachedVocabIds($topic->id);

        $total = count($vocabIds);

        // Không có từ
        if ($total === 0) {
            abort(404, 'Topic này không có từ vựng');
        }

        // Hết từ → trang hoàn thành
        if ($index >= $total) {
            return view('topics.flashcard-finish', compact('topic'));
        }

        /**
         * ✅ LẤY 1 TỪ DUY NHẤT (O(1))
         */
        $vocabulary = Vocabulary::findOrFail($vocabIds[$index]);

        return view('topics.flashcard', compact(
            'topic',
            'vocabulary',
            'index',
            'total'
        ));
    }
    private function getCachedVocabIds(int $topicId): array
    {
        return Cache::remember(
            "topic_{$topicId}_vocab_ids",
            now()->addHours(12),
            fn() => Vocabulary::where('topic_id', $topicId)
                ->orderBy('id', 'asc')
                ->pluck('id')
                ->toArray()
        );
    }


}
