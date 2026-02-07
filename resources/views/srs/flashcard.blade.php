@extends('layouts.app')

@section('header')
    <div>
        <h2 class="text-2xl font-bold text-gray-800">
            🔁 Ôn tập từ vựng
        </h2>
        <p class="text-sm text-gray-500 mt-1">
            Tự nhớ nghĩa trước khi lật thẻ
        </p>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-md mx-auto text-center">

        <!-- FLASHCARD WRAPPER -->
        <div class="card-container mx-auto" onclick="flipCard()">

            <div id="flashcard" class="card">

                <!-- FRONT -->
                <div class="card-face card-front">
                    <h1 class="text-4xl font-bold">
                        {{ $vocabulary->word_kr }}
                    </h1>
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

        <!-- ANSWER -->
        <form method="POST"
              action="{{ route('srs.review.answer') }}"
              class="flex justify-center gap-4 mt-8">
            @csrf

            <input type="hidden" name="progress_id" value="{{ $progress->id }}">

            <button type="submit" name="result" value="wrong"
                    class="px-6 py-3 rounded-xl bg-gray-200 font-semibold hover:bg-gray-300">
                ❌ Chưa nhớ
            </button>

            <button type="submit" name="result" value="correct"
                    class="px-6 py-3 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700">
                ✅ Biết rồi
            </button>
        </form>

    </div>
</div>
@endsection

@section('scripts')
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
        box-shadow: 0 20px 30px rgba(0,0,0,0.1);
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
