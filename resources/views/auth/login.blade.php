@extends('layouts.guest')

@section('title', 'Đăng nhập')

@section('content')
    <div class="w-full max-w-md bg-white rounded-xl shadow p-6">
        <h1 class="text-2xl font-bold text-center mb-6 text-indigo-600">
            🔐 Đăng nhập hệ thống học
        </h1>

        @if (session('status'))
            <div class="mb-4 text-sm text-green-600 text-center">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Email
                </label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                @error('email')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Mật khẩu
                </label>
                <input type="password" name="password" required
                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                @error('password')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember + Forgot --}}
            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center text-gray-600">
                    <input type="checkbox" name="remember"
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2">Ghi nhớ</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-indigo-500 hover:underline">
                        Quên mật khẩu?
                    </a>
                @endif
            </div>

            {{-- Button --}}
            <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition">
                ➜ Đăng nhập
            </button>

            {{-- Register link --}}
            <div class="text-center text-sm text-gray-600 mt-4">
                Chưa có tài khoản?
                <a href="{{ route('register') }}" class="text-indigo-600 font-semibold hover:underline">
                    Đăng ký ngay
                </a>
            </div>
        </form>
    </div>
@endsection