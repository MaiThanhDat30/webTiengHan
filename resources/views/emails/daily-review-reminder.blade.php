<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Nhắc nhở ôn tập từ vựng hôm nay</title>
</head>
<body style="margin:0; padding:0; background:#f5f7fb; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;">

    <div style="max-width:600px; margin:30px auto; background:#ffffff; border-radius:16px; padding:28px; box-shadow:0 10px 30px rgba(0,0,0,0.05);">

        <h2 style="margin-top:0; color:#4f46e5;">
            👋 Chào {{ $user->name }}!
        </h2>

        <p style="font-size:15px; color:#444;">
            Hôm nay là một ngày tuyệt vời để tiếp tục học tiếng Hàn đó ✨  
            Hiện tại bạn có <strong>{{ $items->count() }}</strong> từ vựng cần ôn lại để ghi nhớ chắc hơn.
        </p>

        <div style="background:#f9fafb; border-radius:12px; padding:16px; margin:20px 0;">
            <ul style="padding-left:18px; margin:0;">
                @foreach ($items as $item)
                    <li style="margin-bottom:8px; font-size:14px;">
                        <strong>{{ $item->vocabulary->word_kr ?? 'Từ đã bị xoá' }}</strong>
                        @if($item->vocabulary)
                            – {{ $item->vocabulary->word_vi }}
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>

        <p style="font-size:14px; color:#555;">
            Chỉ cần vài phút ôn tập mỗi ngày là bạn đã tiến bộ hơn hôm qua rồi 💪
        </p>

        <div style="text-align:center; margin:28px 0;">
            <a href="{{ route('srs.review') }}"
               style="display:inline-block; background:#4f46e5; color:#ffffff;
                      padding:12px 26px; border-radius:999px; text-decoration:none;
                      font-weight:600; font-size:15px;">
                🚀 Ôn tập ngay bây giờ
            </a>
        </div>

        <p style="font-size:13px; color:#777; margin-bottom:0;">
            Học đều mỗi ngày – nhớ lâu hơn mỗi ngày 🌱  
            Chúc bạn học tốt và luôn giữ được cảm hứng nhé!
        </p>

        <p style="font-size:13px; color:#999; margin-top:20px;">
            — <br>
            <strong>Web học tiếng Hàn</strong>
        </p>

    </div>

</body>
</html>
