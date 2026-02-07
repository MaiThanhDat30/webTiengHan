@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto text-center">

    <h1 class="text-xl font-bold mb-4">
        🔥 Flashcard: {{ $topic->name }}
    </h1>

    <p class="text-sm text-gray-500 mb-6">
        Từ {{ $index + 1 }} / {{ $total }}
    </p>

    <!-- FLASHCARD -->
    <div class="card-container mx-auto mb-6" onclick="flipCard()">
        <div id="flashcard" class="card">

            <!-- FRONT -->
            <div class="card-face card-front">
                <h2 class="text-4xl font-bold">
                    {{ $vocabulary->word_kr }}
                </h2>
                <p class="text-sm text-gray-400 mt-6">
                    👆 Nhấn để lật nghĩa
                </p>
            </div>

            <!-- BACK -->
            <div class="card-face card-back">
                <p class="text-2xl font-semibold text-indigo-600">
                    {{ $vocabulary->word_vi }}
                </p>
                <p class="text-sm text-gray-400 mt-6">
                    👆 Nhấn để quay lại
                </p>
            </div>

        </div>
    </div>

    <!-- ACTION BUTTONS -->
    <div class="flex justify-between items-center mt-8 gap-3">

        {{-- ⏮ TRƯỚC --}}
        <a href="{{ request()->fullUrlWithQuery(['index' => $index - 1]) }}"
           class="px-4 py-2 rounded-xl bg-gray-200 font-semibold
           {{ $index == 0 ? 'opacity-40 pointer-events-none' : '' }}">
            ⏮ Trước
        </a>

        {{-- ❌ CHƯA THUỘC --}}
        <form action="{{ route('srs.answer') }}" method="POST">
            @csrf
            <input type="hidden" name="vocabulary_id" value="{{ $vocabulary->id }}">
            <input type="hidden" name="topic_id" value="{{ $topic->id }}">
            <input type="hidden" name="index" value="{{ $index }}">
            <input type="hidden" name="result" value="wrong">

            <button class="px-5 py-2 rounded-xl bg-amber-500 text-white font-semibold">
                ❌ Chưa thuộc
            </button>
        </form>

        {{-- ✅ ĐÃ THUỘC --}}
        <form action="{{ route('srs.answer') }}" method="POST">
            @csrf
            <input type="hidden" name="vocabulary_id" value="{{ $vocabulary->id }}">
            <input type="hidden" name="topic_id" value="{{ $topic->id }}">
            <input type="hidden" name="index" value="{{ $index }}">
            <input type="hidden" name="result" value="correct">

            <button class="px-5 py-2 rounded-xl bg-emerald-600 text-white font-semibold">
                ✅ Đã thuộc
            </button>
        </form>

        {{-- ⏭ TIẾP --}}
        <a href="{{ request()->fullUrlWithQuery(['index' => $index + 1]) }}"
           class="px-4 py-2 rounded-xl bg-gray-200 font-semibold">
            ⏭ Tiếp
        </a>
    </div>
</div>

{{-- STYLE + SCRIPT --}}
<style>
    .card-container {
        perspective: 1000px;
        width: 100%;
        max-width: 360px;
        height: 220px;
        cursor: pointer;
    }

    .card {
        width: 100%;
        height: 100%;
        position: relative;
        transform-style: preserve-3d;
        transition: transform 0.6s ease;
    }

    .card.flipped {
        transform: rotateY(180deg);
    }

    .card-face {
        position: absolute;
        inset: 0;
        backface-visibility: hidden;
        background: white;
        border-radius: 1.5rem;
        box-shadow: 0 20px 30px rgba(0,0,0,0.12);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2.5rem;
    }

    .card-back {
        transform: rotateY(180deg);
    }
</style>

<script>
    function flipCard() {
        document.getElementById('flashcard')
            .classList.toggle('flipped');
    }
</script>
@endsection
