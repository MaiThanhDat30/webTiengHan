@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 space-y-10">

        {{-- TIÊU ĐỀ --}}
        <div>
            <h2 class="text-3xl font-bold text-gray-800">
                📊 Dashboard học tập
            </h2>
            <p class="text-gray-500 mt-1">
                Theo dõi tiến độ – cá nhân hóa lộ trình học
            </p>
        </div>

        {{-- THỐNG KÊ NHANH --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl shadow p-4">
                <p class="text-sm text-gray-500">📘 Tổng từ đã học</p>
                <p class="text-3xl font-bold text-indigo-600 mt-1">
                    {{ $totalLearned }}
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow p-4">
                <p class="text-sm text-gray-500">⏰ Từ cần ôn</p>
                <p class="text-3xl font-bold text-amber-500 mt-1">
                    {{ $needReview }}
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow p-4">
                <p class="text-sm text-gray-500">🔥 Chuỗi hiện tại</p>
                <p class="text-3xl font-bold text-emerald-500 mt-1">
                    {{ $currentStreak }}
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow p-4">
                <p class="text-sm text-gray-500">🏆 Kỷ lục streak</p>
                <p class="text-3xl font-bold text-green-600 mt-1">
                    {{ $longestStreak }}
                </p>
            </div>
        </div>

        {{-- HỒ SƠ HỌC TẬP --}}
        <div class="bg-white rounded-2xl shadow p-6 space-y-3">
            <h3 class="text-lg font-bold text-gray-800">
                🧠 Hồ sơ học tập hôm nay
            </h3>

            <div class="flex flex-wrap items-center gap-3">
                <span class="px-4 py-2 rounded-full text-white text-sm font-semibold
                        {{ match ($level) {
        'Mới bắt đầu' => 'bg-gray-500',
        'Chưa ổn định' => 'bg-rose-500',
        'Ổn định' => 'bg-sky-500',
        'Tốt' => 'bg-emerald-500',
        'Rất tốt' => 'bg-indigo-600',
        default => 'bg-gray-400'
    } }}">
                    {{ $level }}
                </span>

                <span class="text-indigo-600 font-semibold">
                    {{ $persona }}
                </span>
            </div>

            <p class="text-sm text-gray-500">
                {{ $personaMessage }}
            </p>
        </div>

        {{-- GỢI Ý LỘ TRÌNH --}}
        <div class="bg-gradient-to-r from-indigo-500 to-purple-500
                        text-white rounded-2xl shadow p-6">
            <h3 class="font-bold text-lg mb-1">
                📌 Gợi ý hôm nay
            </h3>
            <p class="text-sm opacity-90">
                {{ $suggestion }}
            </p>
        </div>

        {{-- BIỂU ĐỒ + TỪ YẾU --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- BIỂU ĐỒ --}}
            <div class="bg-white rounded-2xl shadow p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    📈 Hoạt động 7 ngày gần nhất
                </h3>
                <canvas id="activityChart" height="160"></canvas>
            </div>

            {{-- TỪ HAY SAI --}}
            <div class="bg-white rounded-2xl shadow p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    ⚠️ Từ vựng hay sai / hay quên
                </h3>

                @if($problemVocabs->isEmpty())
                    <p class="text-gray-400 text-sm">
                        Không có từ nào đáng lo 🎉
                    </p>
                @else
                    <div class="space-y-3">
                        @foreach($problemVocabs as $vocab)
                            <div class="flex justify-between items-center border-b pb-2">
                                <div>
                                    <p class="font-semibold text-base">
                                        {{ $vocab->word_kr }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        Sai {{ $vocab->wrongs }}/{{ $vocab->total }} lần
                                    </p>
                                </div>

                                <span class="px-3 py-1 rounded-full text-xs font-semibold text-white
                                                        {{ $vocab->tag == 'Hay quên' ? 'bg-rose-500' : 'bg-amber-500' }}">
                                    {{ $vocab->tag }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- BXH TỪ KHÓ --}}
        <div class="bg-white rounded-2xl shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                🏆 Top từ vựng khó (toàn hệ thống)
            </h3>

            @if($globalWrongRanking->isEmpty())
                <p class="text-gray-400 text-sm">
                    Chưa đủ dữ liệu
                </p>
            @else
                <div class="space-y-3">
                    @foreach($globalWrongRanking as $index => $word)
                        <div class="flex items-center gap-4 border rounded-xl px-4 py-3">
                            <span class="font-bold text-lg">
                                #{{ $index + 1 }}
                            </span>

                            <span class="text-lg font-semibold text-gray-800">
                                {{ $word->word_kr }}
                            </span>

                            @if($index == 0)
                                <span class="ml-auto">👑</span>
                            @elseif($index <= 2)
                                <span class="ml-auto">🔥</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- IDIOM --}}
        {{-- IDIOM --}}
        <div class="bg-gradient-to-br from-emerald-500 via-teal-500 to-cyan-500
                text-white rounded-3xl shadow-lg p-6">

            <div class="flex items-center justify-between mb-5">
                <h3 class="font-bold text-xl flex items-center gap-2">
                    💡 Mẫu câu / Quán dụng ngữ
                </h3>
                <span class="text-xs bg-white/20 px-3 py-1 rounded-full">
                    Gợi ý hôm nay
                </span>
            </div>

            @if($idiomSuggestions->isEmpty())
                <p class="text-sm opacity-80 italic">
                    Chưa có dữ liệu quán dụng ngữ
                </p>
            @else
                <div class="space-y-4">
                    @foreach($idiomSuggestions as $idiom)
                        <div class="bg-white/15 backdrop-blur
                                       rounded-2xl p-5
                                       transition hover:bg-white/25">

                            {{-- Korean --}}
                            <p class="text-2xl font-bold leading-snug">
                                {{ $idiom->sentence_kr }}
                            </p>

                            {{-- Vietnamese --}}
                            <p class="text-base mt-2 font-medium leading-relaxed opacity-95">
                                {{ $idiom->sentence_vi }}
                            </p>


                            {{-- Meta --}}
                            <div class="flex flex-wrap gap-2 mt-4">
                                @if($idiom->level)

                                @endif

                                @if($idiom->tag)

                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- CHART JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        new Chart(document.getElementById('activityChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($last7Days->pluck('date')) !!},
                datasets: [{
                    data: {!! json_encode($last7Days->pluck('total')) !!},
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>
@endsection