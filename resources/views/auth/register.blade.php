@extends('layouts.guest')

@section('title', 'Đăng ký')

@section('content')
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">

        {{-- HEADER --}}
        <div class="text-center mb-8">
            <div class="mx-auto mb-3 w-12 h-12 flex items-center justify-center
                        rounded-full bg-indigo-100 text-indigo-600 text-xl">
                📝
            </div>

            <h1 class="text-2xl font-bold text-gray-800">
                Tạo tài khoản
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Bắt đầu hành trình học tiếng Hàn
            </p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            {{-- Name --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Họ và tên
                </label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    placeholder="Nguyễn Văn A"
                    class="w-full rounded-xl border-gray-300
                           focus:border-indigo-500 focus:ring-indigo-500"
                >
                @error('name')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Email
                </label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    placeholder="example@email.com"
                    class="w-full rounded-xl border-gray-300
                           focus:border-indigo-500 focus:ring-indigo-500"
                >
                @error('email')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Mật khẩu
                </label>
                <input
                    type="password"
                    name="password"
                    required
                    placeholder="••••••••"
                    class="w-full rounded-xl border-gray-300
                           focus:border-indigo-500 focus:ring-indigo-500"
                >
                @error('password')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nhập lại mật khẩu
                </label>
                <input
                    type="password"
                    name="password_confirmation"
                    required
                    placeholder="••••••••"
                    class="w-full rounded-xl border-gray-300
                           focus:border-indigo-500 focus:ring-indigo-500"
                >
                @error('password_confirmation')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- BUTTON --}}
            <button
                type="submit"
                class="w-full bg-indigo-600 text-white py-2.5 rounded-xl
                       font-semibold hover:bg-indigo-700
                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1
                       transition"
            >
                Tạo tài khoản
            </button>

            {{-- LOGIN LINK --}}
            <div class="text-center text-sm text-gray-600 mt-6">
                Đã có tài khoản?
                <a href="{{ route('login') }}"
                   class="text-indigo-600 font-semibold hover:underline">
                    Đăng nhập
                </a>
            </div>

        </form>
    </div>
@endsection
