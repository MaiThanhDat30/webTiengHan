<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>

<h2>👋 Chào {{ $user->name }}</h2>

<p>Hôm nay bạn có <strong>{{ $items->count() }}</strong> từ cần ôn:</p>

<ul>
    @foreach ($items as $item)
        <li>
            <b>{{ $item->vocabulary->word_kr }}</b>
            – {{ $item->vocabulary->word_vi }}
        </li>
    @endforeach
</ul>

<p>
    👉 <a href="{{ url('/review') }}">Nhấn vào đây để ôn ngay</a>
</p>

<p>💪 Học đều mỗi ngày – nhớ rất lâu!</p>

</body>
</html>
