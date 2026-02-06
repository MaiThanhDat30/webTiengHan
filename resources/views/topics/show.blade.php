@extends('layouts.app')

@section('content')

    <h1 class="text-2xl font-bold mb-4">
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
                   class="bg-white p-5 rounded-xl shadow hover:shadow-md transition">
                    📘 {{ $child->name }}
                </a>
            @endforeach
        </div>

    {{-- 📚 DANH SÁCH TỪ VỰNG (CÓ PHÂN TRANG) --}}
    @else
        @if ($vocabularies->count())
            <ul class="space-y-3">
                @foreach ($vocabularies as $vocab)
                    <li class="bg-white p-4 rounded-lg shadow">
                        <span class="font-semibold text-lg">
                            {{ $vocab->word_kr }}
                        </span>
                        <span class="text-gray-600">
                            – {{ $vocab->word_vi }}
                        </span>
                    </li>
                @endforeach
            </ul>

            {{-- 🔄 NEXT / PREV --}}
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
