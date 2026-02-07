@extends('layouts.guest')

@section('title', 'Xác nhận mật khẩu')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-md w-full max-w-md">
    <h1 class="text-xl font-bold mb-4 text-center">🔒 Xác nhận mật khẩu</h1>

    <p class="text-sm text-gray-600 mb-4 text-center">
        Đây là khu vực bảo mật.  
        Vui lòng nhập lại mật khẩu để tiếp tục.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">
                Mật khẩu
            </label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
            >
            @error('password')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            class="mt-6 w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700"
        >
            Xác nhận
        </button>
    </form>
</div>
@endsection
