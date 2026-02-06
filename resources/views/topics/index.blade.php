@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-6">📘 Danh sách chủ đề</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($topics as $topic)
            <div class="bg-white rounded-xl shadow p-5">
                <h2 class="text-lg font-semibold text-indigo-600">
                    {{ $topic->name }}
                </h2>

                {{-- Nếu topic có chủ đề con (ví dụ: TOPIK) --}}
                @if ($topic->children->count())
                    <ul class="mt-4 space-y-2">
                        @foreach ($topic->children as $child)
                            <li>
                                <a href="{{ route('topics.show', $child->id) }}"
                                   class="text-gray-700 hover:text-indigo-600">
                                    📘 {{ $child->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    {{-- Chủ đề thường --}}
                    <a href="{{ route('topics.show', $topic->id) }}"
                       class="inline-block mt-4 text-indigo-500 hover:underline">
                        ➜ Vào học
                    </a>
                @endif
            </div>
        @empty
            <p class="text-gray-500">⚠️ Chưa có chủ đề nào</p>
        @endforelse
    </div>
@endsection
