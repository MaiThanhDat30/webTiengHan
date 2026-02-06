@extends('layouts.app')

@section('header')
    <div>
        <h2 class="text-2xl font-bold text-gray-800">
            📚 Từ cần ôn
        </h2>
        <p class="text-sm text-gray-500 mt-1">
            Những từ bạn chưa nhớ hoặc đã đến hạn ôn lại
        </p>
    </div>
@endsection

@section('content')
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4">

            {{-- EMPTY STATE --}}
            @if ($reviews->isEmpty())
                <div class="bg-white rounded-2xl border border-dashed border-gray-300 p-10 text-center">
                    <div class="text-5xl mb-4">🎉</div>

                    <p class="text-lg font-semibold text-gray-700">
                        Tuyệt vời! Không có từ nào cần ôn
                    </p>

                    <p class="text-sm text-gray-500 mt-2">
                        Hãy quay lại học thêm từ mới nhé
                    </p>

                    <a href="{{ url('/topics') }}"
                       class="inline-block mt-6 px-6 py-3 rounded-xl
                              bg-indigo-600 text-white font-medium
                              hover:bg-indigo-700 transition">
                        📘 Học theo chủ đề
                    </a>
                </div>
            @else

                {{-- REVIEW LIST --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($reviews as $item)
                        <div class="bg-white rounded-2xl border border-gray-200 p-6
                                    hover:shadow-md transition group">

                            <div class="flex items-center justify-between">
                                <h3 class="text-xl font-bold text-gray-800">
                                    {{ $item->vocabulary->word_kr }}
                                </h3>

                                <span class="text-sm text-gray-400">
                                    {{ $item->next_review_at->format('d/m/Y') }}
                                </span>
                            </div>

                            <p class="text-gray-500 mt-2">
                                {{ $item->vocabulary->word_vi }}
                            </p>

                            <a href="{{ route('srs.card', $item->id) }}"
                               class="mt-6 inline-flex items-center justify-center w-full
                                      px-4 py-3 rounded-xl
                                      bg-indigo-50 text-indigo-600 font-semibold
                                      hover:bg-indigo-600 hover:text-white transition">
                                🔁 Ôn lại ngay
                            </a>
                        </div>
                    @endforeach
                </div>

            @endif

        </div>
    </div>
@endsection
