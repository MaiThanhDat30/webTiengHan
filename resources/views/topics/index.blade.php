@extends('layouts.app')

@section('content')
    <div class="space-y-10">

        {{-- TIÊU ĐỀ --}}
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                📘 Danh sách chủ đề
            </h1>
            <p class="text-gray-500 mt-1">
                Chọn chủ đề bạn muốn học hoặc ôn tập
            </p>
        </div>

        {{-- GRID --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            @forelse ($topics as $topic)

                {{-- =========================
                TOPIC CÓ CHỦ ĐỀ CON
                ========================= --}}
                @if ($topic->children->count())
                    <div class="bg-white rounded-2xl shadow
                        p-6 flex flex-col
                        hover:shadow-lg transition">

                        {{-- HEADER --}}
                        <div class="flex items-start justify-between">
                            <h2 class="text-lg font-bold text-indigo-600 leading-tight">
                                {{ $topic->name }}
                            </h2>

                            <span class="text-xs font-semibold
                                               bg-indigo-50 text-indigo-600
                                               px-3 py-1 rounded-full">
                                {{ $topic->children->count() }} chủ đề
                            </span>
                        </div>

                        {{-- CHILD TOPICS --}}
                        <div class="mt-5 space-y-2 max-h-64 overflow-y-auto pr-1">
                            @foreach ($topic->children as $child)
                                <a href="{{ route('topics.show', $child->id) }}" class="flex items-center justify-between
                                                      bg-gray-50 rounded-xl
                                                      px-4 py-2
                                                      hover:bg-gray-100 transition
                                                      focus:outline-none focus:ring-2 focus:ring-indigo-400">

                                    <span class="flex items-center gap-2 text-gray-700">
                                        📘
                                        <span class="text-sm font-medium">
                                            {{ $child->name }}
                                        </span>
                                    </span>

                                    <span class="text-xs text-gray-400">
                                        ➜
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- =========================
                    TOPIC KHÔNG CÓ CON
                    👉 CARD = LINK (FIX DOUBLE CLICK)
                    ========================= --}}
                @else
                    <a href="{{ route('topics.show', $topic->id) }}" class="block bg-white rounded-2xl shadow
                                      p-6 flex flex-col justify-between
                                      hover:shadow-lg transition
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400">

                        <h2 class="text-lg font-bold text-indigo-600 leading-tight">
                            {{ $topic->name }}
                        </h2>

                        <span class="mt-6 inline-flex items-center justify-center gap-2
                                           px-4 py-2 rounded-xl
                                           bg-indigo-600 text-white
                                           font-semibold text-sm
                                           hover:bg-indigo-700 transition">
                            🚀 Vào học
                        </span>
                    </a>
                @endif

            @empty
                {{-- EMPTY --}}
                <div class="col-span-full">
                    <div class="bg-white rounded-2xl shadow p-10 text-center">
                        <p class="text-4xl">📭</p>
                        <p class="text-gray-500 mt-3">
                            Chưa có chủ đề nào
                        </p>
                    </div>
                </div>
            @endforelse

        </div>
    </div>
@endsection