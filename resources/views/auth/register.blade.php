@extends('layouts.guest')

@section('title', 'Đăng ký')

@section('content')
<div class="w-full max-w-md bg-white rounded-xl shadow p-6">
    <h1 class="text-2xl font-bold text-center mb-6 text-indigo-600">
        📝 Đăng ký tài khoản
    </h1>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
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
                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
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
                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
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
                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
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
                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
            >
            @error('password_confirmation')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-between text-sm mt-2">
            <a href="{{ route('login') }}"
               class="text-indigo-500 hover:underline">
                Đã có tài khoản?
            </a>

            <button
                type="submit"
                class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition">
                ➜ Đăng ký
            </button>
        </div>
    </form>
</div>
@endsection
