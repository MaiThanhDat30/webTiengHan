@extends('layouts.guest')

@section('title', 'Đăng nhập')

@section('content')
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">

        {{-- HEADER --}}
        <div class="text-center mb-8">
            <div class="mx-auto mb-3 w-12 h-12 flex items-center justify-center
                        rounded-full bg-indigo-100 text-indigo-600 text-xl">
                🔐
            </div>

            <h1 class="text-2xl font-bold text-gray-800">
                Đăng nhập
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Tiếp tục hành trình học tiếng Hàn
            </p>
        </div>

        @if (session('status'))
            <div class="mb-4 text-sm text-green-600 text-center">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

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
                    autofocus
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

                <div class="relative">
                    <input
                        id="login-password"
                        type="password"
                        name="password"
                        required
                        class="w-full pr-12 rounded-xl border-gray-300
                               focus:border-indigo-500 focus:ring-indigo-500"
                    >

                    <button type="button"
                            onclick="togglePassword('login-password', this)"
                            class="absolute inset-y-0 right-3 flex items-center
                                   text-gray-400 hover:text-indigo-600">
                        👁️
                    </button>
                </div>

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
                    <a href="{{ route('password.request') }}"
                       class="text-indigo-500 hover:underline">
                        Quên mật khẩu?
                    </a>
                @endif
            </div>

            {{-- SUBMIT --}}
            <button
                type="submit"
                class="w-full bg-indigo-600 text-white py-2.5 rounded-xl
                       font-semibold hover:bg-indigo-700 transition"
            >
                ➜ Đăng nhập
            </button>

            {{-- REGISTER --}}
            <div class="text-center text-sm text-gray-600 mt-6">
                Chưa có tài khoản?
                <a href="{{ route('register') }}"
                   class="text-indigo-600 font-semibold hover:underline">
                    Đăng ký ngay
                </a>
            </div>

        </form>
    </div>

    {{-- SCRIPT --}}
    @push('scripts')
        <script>
            function togglePassword(id, btn) {
                const input = document.getElementById(id);

                if (input.type === 'password') {
                    input.type = 'text';
                    btn.innerText = '🙈';
                } else {
                    input.type = 'password';
                    btn.innerText = '👁️';
                }
            }
        </script>
    @endpush
@endsection
