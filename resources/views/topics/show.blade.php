@extends('layouts.app')

@section('content')

    {{-- TIÊU ĐỀ --}}
    <h1 class="text-2xl font-bold mb-4 text-gray-800">
        {{ $topic->name }}
    </h1>

    {{-- 🔥 FLASHCARD --}}
    @if ($topic->children->count() === 0 && $vocabularies->count())
        <div class="mb-6">
            <a href="{{ route('topics.flashcard', $topic->id) }}"
               class="inline-flex items-center gap-2 px-6 py-3
                      bg-indigo-600 text-white font-semibold rounded-xl
                      hover:bg-indigo-700 transition shadow">
                🔥 Học Flashcard
            </a>
        </div>
    @endif

    {{-- 📘 CHỦ ĐỀ CON --}}
    @if ($topic->children->count())

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($topic->children as $child)
                <a href="{{ route('topics.show', $child->id) }}"
                   class="bg-white p-5 rounded-xl shadow
                          hover:shadow-md transition
                          flex items-center justify-between">

                    <span class="font-medium text-gray-800">
                        📘 {{ $child->name }}
                    </span>

                    <span class="text-gray-400">➜</span>
                </a>
            @endforeach
        </div>

    {{-- 📚 DANH SÁCH TỪ VỰNG --}}
    @else

        @if ($vocabularies->count())

            <ul class="space-y-3">
                @foreach ($vocabularies as $vocab)

                    @php
                        $isSaved = \App\Models\UserVocabProgress::where('user_id', auth()->id())
                            ->where('vocabulary_id', $vocab->id)
                            ->exists();
                    @endphp

                    <li class="bg-white p-4 rounded-lg shadow
                               flex items-center justify-between gap-4
                               hover:shadow-md transition">

                        {{-- WORD --}}
                        <div>
                            <span class="font-semibold text-lg text-gray-800">
                                {{ $vocab->word_kr }}
                            </span>
                            <span class="text-gray-600">
                                – {{ $vocab->word_vi }}
                            </span>
                        </div>

                        {{-- TOGGLE SAVE REVIEW --}}
                        <form action="{{ route('srs.toggle') }}" method="POST">
                            @csrf

                            <input type="hidden" name="vocabulary_id" value="{{ $vocab->id }}">

                            <button
                                class="text-xs px-3 py-1.5 rounded-full transition
                                {{ $isSaved
                                    ? 'bg-indigo-600 text-white font-semibold'
                                    : 'border border-indigo-200 text-indigo-600 hover:bg-indigo-600 hover:text-white'
                                }}">
                                ⭐ {{ $isSaved ? 'Đã lưu ôn' : 'Lưu ôn' }}
                            </button>
                        </form>

                    </li>
                @endforeach
            </ul>

            {{-- PAGINATION --}}
            <div class="mt-6">
                {{ $vocabularies->links() }}
            </div>

        @else
            <p class="text-gray-500">
                ⚠️ Chưa có từ vựng cho chủ đề này
            </p>
        @endif

    @endif

@endsection
