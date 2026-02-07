<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif;">
    <h2>👋 Chào {{ $user->name }}</h2>

    <p>
        Hôm nay bạn có <b>{{ $count }}</b> từ vựng cần ôn lại.
    </p>

    <h4>🧠 Một số từ bạn hay quên:</h4>
    <ul>
        @foreach ($vocabs as $vocab)
            <li>
                {{ $vocab->word_kr }} (sai {{ $vocab->wrongs }} lần)
            </li>
        @endforeach
    </ul>

    <p>
        👉 <a href="{{ route('srs.review') }}">
            Ôn ngay để nhớ lâu hơn
        </a>
    </p>

    <p>🔥 Học đều mỗi ngày là cách học nhanh nhất.</p>
</body>
</html>
