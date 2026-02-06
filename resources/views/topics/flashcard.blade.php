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
        <div onclick="flipCard()" class="bg-white rounded-3xl shadow-xl p-10 cursor-pointer
                        hover:scale-[1.02] transition">

            <!-- FRONT -->
            <h2 class="text-4xl font-bold">
                {{ $vocabulary->word_kr }}
            </h2>

            <!-- BACK -->
            <p id="meaning" class="hidden mt-6 text-2xl font-semibold text-indigo-600">
                {{ $vocabulary->word_vi }}
            </p>

            <p class="text-sm text-gray-400 mt-6">
                👆 Nhấn để lật nghĩa
            </p>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="flex justify-between items-center mt-8 gap-3">

            {{-- ⏮ TRƯỚC --}}
            <a href="{{ request()->fullUrlWithQuery(['index' => $index - 1]) }}" class="px-4 py-2 rounded-xl bg-gray-200 font-semibold
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

    <script>
        function flipCard() {
            document.getElementById('meaning').classList.toggle('hidden');
        }
    </script>
@endsection