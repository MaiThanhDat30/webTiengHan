<x-app-layout>
    <h1 class="text-xl font-bold mb-4">Từ vựng của tôi</h1>

    @foreach ($vocabularies as $vocab)
        <div class="border p-2 mb-2">
            <strong>{{ $vocab->korean }}</strong>
            – {{ $vocab->meaning }}
        </div>
    @endforeach
    <a href="/vocab/create">➕ Thêm từ mới</a>

</x-app-layout>
