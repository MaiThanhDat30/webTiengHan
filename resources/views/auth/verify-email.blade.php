@extends('layouts.guest')

@section('title', 'Xác thực email')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-md w-full max-w-md text-center">
    <h1 class="text-xl font-bold mb-4">📩 Xác thực email</h1>

    <p class="text-gray-600 mb-4">
        Cảm ơn bạn đã đăng ký!  
        Vui lòng kiểm tra email và nhấn vào link xác thực để tiếp tục.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="text-green-600 mb-4">
            ✅ Link xác thực đã được gửi lại!
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
            Gửi lại email xác thực
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-4">
        @csrf
        <button class="text-sm text-gray-500 hover:underline">
            Đăng xuất
        </button>
    </form>
</div>
@endsection
