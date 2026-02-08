@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8 space-y-10">

        {{-- =========================
        👤 HỒ SƠ NGƯỜI DÙNG
        ========================= --}}
        <div class="bg-white rounded-2xl border p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            {{-- LEFT --}}
            <div class="flex items-center gap-4">
                {{-- AVATAR --}}
                <div
                    class="w-14 h-14 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 text-xl font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

                {{-- INFO --}}
                <div>
                    <h2 class="text-lg font-bold">
                        👋 Xin chào, {{ auth()->user()->name }}
                    </h2>
                    <p class="text-sm text-gray-500">
                        {{ auth()->user()->email }}
                    </p>

                    @if(!empty($badges))
                        <div class="flex gap-2 mt-1 flex-wrap">
                            @foreach($badges as $badge)
                                <span class="text-xs px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded-full">
                                    {{ $badge }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- RIGHT --}}
            <div>
                <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium
                      bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                    ✏️ Chỉnh sửa hồ sơ
                </a>
            </div>

        </div>
        {{-- =========================
        📊 TỔNG QUAN
        ========================= --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl p-5 border">
                <p class="text-sm text-gray-500">📘 Tổng từ đã học</p>
                <p class="text-3xl font-bold mt-1">{{ $totalLearned }}</p>
            </div>

            <div class="bg-white rounded-2xl p-5 border">
                <p class="text-sm text-gray-500">🔁 Tổng lượt ôn</p>
                <p class="text-3xl font-bold mt-1">{{ $totalReviews }}</p>
            </div>

            <div class="bg-white rounded-2xl p-5 border">
                <p class="text-sm text-gray-500">⏰ Cần ôn hôm nay</p>
                <p class="text-3xl font-bold mt-1 text-red-600">{{ $dueToday }}</p>
            </div>
        </div>

        {{-- =========================
        🧠 MỨC ĐỘ GHI NHỚ
        ========================= --}}
        <div class="bg-white rounded-2xl p-6 border">
            <h3 class="text-lg font-semibold mb-4">🧠 Mức độ ghi nhớ</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="p-4 rounded-xl bg-green-50 border border-green-200">
                    <p class="font-semibold text-green-700">Nhớ lâu</p>
                    <p class="text-2xl font-bold">
                        {{ data_get($memoryLevels, 'long', collect())->count() }}
                    </p>
                    <p class="text-xs text-green-600">≥ 14 ngày</p>
                </div>

                <div class="p-4 rounded-xl bg-yellow-50 border border-yellow-200">
                    <p class="font-semibold text-yellow-700">Đang nhớ</p>
                    <p class="text-2xl font-bold">
                        {{ data_get($memoryLevels, 'mid', collect())->count() }}
                    </p>
                    <p class="text-xs text-yellow-600">3 – 13 ngày</p>
                </div>

                <div class="p-4 rounded-xl bg-gray-50 border">
                    <p class="font-semibold text-gray-700">Mới học</p>
                    <p class="text-2xl font-bold">
                        {{ data_get($memoryLevels, 'new', collect())->count() }}
                    </p>
                    <p class="text-xs text-gray-500">&lt; 3 ngày</p>
                </div>
            </div>
        </div>

        {{-- =========================
        ⏳ SẮP QUAY LẠI ÔN (SRS)
        ========================= --}}
        @if($upcomingReviews->isNotEmpty())
            <div class="w-full">
                <div class="bg-white p-5 rounded-2xl border mb-6">
                    <h3 class="text-sm font-semibold mb-3">
                        ⏳ Từ sắp quay lại ôn
                    </h3>

                    <ul class="space-y-2">
                        @foreach($upcomingReviews as $item)
                            @php
                                $days = now()->diffInDays($item->next_review_at);
                            @endphp
                            <li class="flex justify-between items-center">
                                <span class="text-lg font-semibold">
                                    {{ $item->vocabulary->word_kr ?? '[Đã xoá]' }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ $days === 0 ? 'Hôm nay' : "Còn $days ngày" }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif


        {{-- =========================
        📚 DANH SÁCH TỪ + PAGINATION
        ========================= --}}
        <div class="w-full">
            <div class="bg-white border rounded-2xl p-6 space-y-5">

                <h3 class="text-base font-semibold text-center">
                    📚 Từ đã học & ôn tập
                </h3>

                @if($words->isEmpty())
                    <p class="text-sm text-gray-500 text-center">
                        Chưa học từ nào.
                    </p>
                @else
                    <div class="space-y-3">
                        @foreach($words as $word)
                            @php
                                $total = max(1, $word->correct_count + $word->wrong_count);
                                $percent = round(($word->correct_count / $total) * 100);
                            @endphp

                            <div class="p-4 border-b last:border-b-0 hover:bg-gray-50 transition">

                                {{-- ROW --}}
                                <div class="grid grid-cols-12 items-center gap-4">

                                    {{-- WORD --}}
                                    <div class="col-span-7">
                                        <p class="text-lg font-semibold leading-tight">
                                            {{ $word->word_kr ?? '[Đã xoá]' }}
                                        </p>
                                        @if($word->word_vi)
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                {{ $word->word_vi }}
                                            </p>
                                        @endif
                                    </div>

                                    {{-- RIGHT INFO --}}
                                    <div class="col-span-5 text-right text-xs text-gray-500 space-x-3">
                                        <span>
                                            🔁 {{ $word->review_count }} lần
                                        </span>
                                        <span class="text-green-600 font-medium">
                                            ✓ {{ $word->correct_count }}
                                        </span>
                                        <span class="text-red-500 font-medium">
                                            ✗ {{ $word->wrong_count }}
                                        </span>
                                    </div>

                                </div>


                            </div>
                        @endforeach
                    </div>

                    {{-- PAGINATION --}}
                    @if($words->hasPages())
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-4 text-sm text-gray-500">

                            <div>
                                Showing
                                <span class="font-medium text-gray-700">{{ $words->firstItem() }}</span>
                                to
                                <span class="font-medium text-gray-700">{{ $words->lastItem() }}</span>
                                of
                                <span class="font-medium text-gray-700">{{ $words->total() }}</span>
                                results
                            </div>

                            <div>
                                {{ $words->onEachSide(1)->links() }}
                            </div>

                        </div>
                    @endif
                @endif
            </div>
        </div>


        {{-- =========================
        💡 GỢI Ý
        ========================= --}}
        <div class="w-full mb-6">
            <div class="flex items-start gap-3 bg-indigo-50 border border-indigo-200 rounded-2xl p-5 text-sm">

                {{-- ICON --}}
                <div class="text-xl leading-none">
                    💡
                </div>

                {{-- CONTENT --}}
                <div class="flex-1">
                    <p class="font-semibold text-indigo-700 mb-1">
                        Gợi ý học tập
                    </p>

                    <p class="text-gray-700">
                        @if($dueToday > 0)
                            Bạn có
                            <span class="font-semibold text-indigo-600">
                                {{ $dueToday }}
                            </span>
                            từ đến hạn ôn hôm nay. Nên ôn ngay để giữ nhịp ghi nhớ.
                        @elseif($totalReviews < 20)
                            Bạn nên ôn lại một số từ đã học để ghi nhớ lâu hơn.
                        @else
                            Tiến độ rất tốt, tiếp tục duy trì nhé! 🚀
                        @endif
                    </p>
                </div>

            </div>
        </div>
    </div>
@endsection