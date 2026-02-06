<h2>Danh sách từ vựng</h2>

@foreach ($vocabularies as $vocab)
    <div style="margin-bottom: 10px;">
        <strong>{{ $vocab->word_kr }}</strong>
        <button onclick="this.nextElementSibling.style.display='block'">
            Hiện nghĩa
        </button>
        <span style="display:none;"> → {{ $vocab->word_vi }}</span>
    </div>
@endforeach
