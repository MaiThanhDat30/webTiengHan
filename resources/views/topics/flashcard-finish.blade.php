@extends('layouts.app')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center">
    <div class="bg-white rounded-3xl shadow-xl p-10 max-w-md text-center space-y-6">

        <h1 class="text-3xl font-bold text-emerald-600">
            🎉 Hoàn thành Flashcard
        </h1>

        <p class="text-gray-600 text-lg">
            Bạn đã học xong toàn bộ từ vựng trong chủ đề:
        </p>

        <p class="text-2xl font-bold text-indigo-600">
            {{ $topic->name }}
        </p>

        <div class="flex flex-col gap-3 mt-6">
            <a href="{{ route('topics.show', $topic->id) }}"
               class="px-6 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700">
                🔁 Học lại chủ đề
            </a>

            <a href="{{ route('topics.index') }}"
               class="px-6 py-3 rounded-xl bg-gray-200 font-semibold hover:bg-gray-300">
                📚 Quay lại danh sách chủ đề
            </a>
        </div>

    </div>
</div>
@endsection
