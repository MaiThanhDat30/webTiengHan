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

    {{-- FLASHCARD --}}
    <div class="card-container mx-auto" onclick="flipCard()">
        <div id="flashcard" class="card">

            <div class="card-face card-front">
                <h1 id="word-kr" class="text-4xl font-bold"></h1>
                <p class="text-sm text-gray-400 mt-6">
                    👆 Nhấn để lật nghĩa
                </p>
            </div>

            <div class="card-face card-back">
                <p id="word-vi" class="text-2xl font-semibold text-indigo-600"></p>
                <p class="text-sm text-gray-400 mt-6">
                    👆 Nhấn để quay lại
                </p>
            </div>

        </div>
    </div>

    {{-- ACTION --}}
    <div class="flex justify-center gap-3 mt-8">
        <button onclick="answer('wrong')" class="px-6 py-3 rounded-xl bg-gray-200 font-semibold">
            ❌ Chưa nhớ
        </button>

        <button onclick="answer('correct')" class="px-6 py-3 rounded-xl bg-emerald-600 text-white font-semibold">
            ✅ Biết rồi
        </button>
    </div>

</div>
</div>
@endsection

@section('scripts')
<style>
.card-container {
    perspective: 1000px;
    max-width: 360px;
    height: 220px;
    cursor: pointer;
}
.card {
    width: 100%;
    height: 100%;
    position: relative;
    transform-style: preserve-3d;
    transition: transform .25s ease;
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
    box-shadow: 0 20px 30px rgba(0,0,0,.12);
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
/* ========= DATA (PRELOAD 1 LẦN) ========= */
const cards = @json($cards);
let index = 0;
const csrf = '{{ csrf_token() }}';

/* ========= CARD ========= */
function flipCard() {
    document.getElementById('flashcard').classList.toggle('flipped');
}

function render() {
    const card = cards[index];
    document.getElementById('flashcard').classList.remove('flipped');
    document.getElementById('word-kr').innerText = card.word_kr;
    document.getElementById('word-vi').innerText = card.word_vi;
}

/* ========= NEXT ========= */
function nextCard() {
    index++;

    if (index >= cards.length) {
        window.location.href = "{{ route('srs.review') }}";
        return;
    }

    render();
}

/* ========= ANSWER ========= */
function answer(result) {
    const card = cards[index];

    // 🚀 đổi thẻ NGAY (0ms)
    nextCard();

    // 🔥 gửi API ngầm
    fetch("{{ route('srs.review.answer') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf,
        },
        body: JSON.stringify({
            progress_id: card.id,
            result: result
        })
    });
}

/* ========= INIT ========= */
render();
</script>
@endsection
