<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\URL;
use App\Services\ResendMailService;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        /** 🔐 TẠO LINK XÁC THỰC */
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        /** ✉️ GỬI MAIL BẰNG RESEND (KHÔNG SMTP) */
        ResendMailService::send(
            $user->email,
            'Xác thực email tài khoản',
            "
    <p>Xin chào <strong>{$user->name}</strong>,</p>

    <p>
        Cảm ơn bạn đã đăng ký và sử dụng dịch vụ của chúng tôi.
        Để hoàn tất quá trình tạo tài khoản và đảm bảo an toàn cho thông tin cá nhân,
        vui lòng xác thực địa chỉ email của bạn bằng cách nhấn vào liên kết bên dưới.
    </p>

    <p>
        👉 <a href='{$url}'>Xác thực email</a>
    </p>

    <p>
        Liên kết xác thực này chỉ có hiệu lực trong một khoảng thời gian nhất định.
        Nếu bạn không thực hiện yêu cầu này, bạn có thể bỏ qua email này một cách an tâm.
    </p>

    <p>
        Trân trọng,<br>
        <strong>Đội ngũ hỗ trợ</strong>
    </p>
    "
        );


        return redirect()->route('login')->with(
            'status',
            'Đăng ký thành công! Vui lòng kiểm tra email để xác thực.'
        );
    }

}
