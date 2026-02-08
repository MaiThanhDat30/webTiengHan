@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto text-center">

    <h1 class="text-xl font-bold mb-4">
        🔥 Flashcard: {{ $topic->name }}
    </h1>

    <p id="progressText" class="text-sm text-gray-500 mb-6">
        Từ {{ $index + 1 }} / {{ $total }}
    </p>

    <!-- FLASHCARD -->
    <div class="card-container mx-auto mb-6" onclick="flipCard()">
        <div id="flashcard" class="card">

            <!-- FRONT -->
            <div class="card-face card-front">
                <h2 id="wordKr" class="text-4xl font-bold">
                    {{ $vocabulary->word_kr }}
                </h2>
                <p class="text-sm text-gray-400 mt-6">
                    👆 Nhấn để lật nghĩa
                </p>
            </div>

            <!-- BACK -->
            <div class="card-face card-back">
                <p id="wordVi" class="text-2xl font-semibold text-indigo-600">
                    {{ $vocabulary->word_vi }}
                </p>
                <p class="text-sm text-gray-400 mt-6">
                    👆 Nhấn để quay lại
                </p>
            </div>

        </div>
    </div>

    <!-- ACTION BUTTONS -->
    <div class="flex justify-center gap-4 mt-6">
        <button onclick="answer('wrong')"
            class="px-6 py-2 rounded-xl bg-amber-500 text-white font-semibold">
            ❌ Chưa thuộc
        </button>

        <button onclick="answer('correct')"
            class="px-6 py-2 rounded-xl bg-emerald-600 text-white font-semibold">
            ✅ Đã thuộc
        </button>
    </div>
</div>

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
    transition: transform 0.35s ease;
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
/* ================= STATE ================= */
let topicId = {{ $topic->id }};
let total = {{ $total }};
let currentIndex = {{ $index }};
let csrf = '{{ csrf_token() }}';

let currentVocab = {
    id: {{ $vocabulary->id }},
    word_kr: @json($vocabulary->word_kr),
    word_vi: @json($vocabulary->word_vi),
};

let buffer = [];
let saving = false;

/* ================= UI ================= */
function flipCard() {
    document.getElementById('flashcard')
        .classList.toggle('flipped');
}

function renderCard(vocab) {
    currentVocab = vocab;

    document.getElementById('flashcard')
        .classList.remove('flipped');

    document.getElementById('wordKr').innerText = vocab.word_kr;
    document.getElementById('wordVi').innerText = vocab.word_vi;

    document.getElementById('progressText').innerText =
        `Từ ${currentIndex + 1} / ${total}`;
}

/* ================= PRELOAD ================= */
async function preloadNext() {
    if (buffer.length >= 3) return;

    const start = currentIndex + buffer.length + 1;
    if (start >= total) return;

    try {
        const res = await fetch(
            `/topics/${topicId}/flashcard/preload?start=${start}`
        );
        if (!res.ok) return;

        const data = await res.json();
        buffer.push(...data);
    } catch (e) {
        console.warn('Preload fail', e);
    }
}

/* ================= NEXT ================= */
function nextCard() {
    if (buffer.length > 0) {
        const next = buffer.shift();
        currentIndex++;
        renderCard(next);
        preloadNext();
    } else {
        // fallback an toàn
        window.location.href =
            `{{ request()->url() }}?index=${currentIndex + 1}`;
    }
}

/* ================= ANSWER (CỰC KỲ QUAN TRỌNG) ================= */
function answer(result) {
    if (saving) return;
    saving = true;

    const answeredId = currentVocab.id;

    // ⚡ UI đổi NGAY
    nextCard();

    // 📡 LƯU NGẦM – KHÔNG CHỜ
    fetch("{{ route('srs.answer') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf,
        },
        body: JSON.stringify({
            vocabulary_id: answeredId,
            result: result
        }),
        keepalive: true // ⭐ QUAN TRỌNG
    }).catch(() => {});

    setTimeout(() => saving = false, 80);
}

/* 🚀 PRELOAD NGAY */
preloadNext();
preloadNext();
</script>
@endsection
