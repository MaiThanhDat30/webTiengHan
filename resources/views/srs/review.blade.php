@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- TIÊU ĐỀ --}}
    <div>
        <h2 class="text-2xl font-bold text-gray-800">
            🔁 Ôn tập từ vựng
        </h2>
        <p class="text-gray-500 mt-1">
            Các từ đến hạn cần ôn theo hệ thống SRS
        </p>
    </div>

    {{-- DANH SÁCH TỪ ÔN --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        @forelse ($reviews as $item)
            @php
                $daysLeft = $item->next_review_at
                    ? now()->diffInDays($item->next_review_at, false)
                    : 0;

                $isDue = $daysLeft <= 0;
            @endphp

            <div class="bg-white rounded-2xl border p-6 transition
                        {{ $isDue ? 'border-red-300 bg-red-50' : 'border-gray-200 hover:shadow-md' }}">

                {{-- HEADER --}}
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-800">
                        {{ $item->vocabulary->word_kr ?? 'Từ đã bị xoá' }}
                    </h3>

                    <span class="text-xs px-3 py-1 rounded-full
                        {{ $isDue ? 'bg-red-200 text-red-800' : 'bg-gray-100 text-gray-600' }}">
                        {{ $stepsLabel[$item->step] ?? 'Ôn tập' }}
                    </span>
                </div>

                {{-- MEANING --}}
                <p class="text-gray-600 mt-2">
                    {{ $item->vocabulary->word_vi ?? '' }}
                </p>

                {{-- REVIEW INFO --}}
                <p class="text-sm mt-3
                    {{ $isDue ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                    @if ($isDue)
                        ⏰ Đến hạn ôn hôm nay
                    @else
                        📅 Ôn sau {{ $daysLeft }} ngày
                        ({{ $item->next_review_at?->format('d/m/Y') }})
                    @endif
                </p>

                {{-- ACTION --}}
                <a href="{{ route('srs.card', $item->id) }}"
                   class="mt-5 inline-flex items-center justify-center w-full
                          px-4 py-3 rounded-xl font-semibold transition
                          {{ $isDue
                                ? 'bg-red-600 text-white hover:bg-red-700'
                                : 'bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white'
                          }}">
                    🔁 Ôn ngay
                </a>
            </div>

        @empty
            {{-- KHÔNG CÓ TỪ CẦN ÔN --}}
            <div class="col-span-full">
                <div class="bg-white rounded-2xl shadow p-10 text-center">
                    <p class="text-4xl">🎉</p>
                    <h3 class="text-xl font-bold text-gray-700 mt-4">
                        Không có từ cần học
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Bạn đã ôn xong tất cả từ đến hạn hôm nay
                    </p>
                </div>
            </div>
        @endforelse

    </div>
</div>
@endsection
