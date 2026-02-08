@extends('layouts.app')

{{-- =========================
PAGE HEADER
========================= --}}
@section('header')
    <h2 class="font-semibold text-xl text-gray-800">
        {{ __('Profile') }}
    </h2>
@endsection

{{-- =========================
PAGE CONTENT
========================= --}}
@section('content')

    @php
        $dueToday = $dueToday ?? 0;
        $totalReviews = $totalReviews ?? 0;
    @endphp

    <div class="space-y-8">

        {{-- =========================
        💡 GỢI Ý
        ========================= --}}
        <div class="rounded-2xl p-6 shadow-sm
                    bg-gradient-to-r from-indigo-50 to-blue-50
                    border border-indigo-200">
            <div class="flex items-start gap-4">
                <div class="flex items-center justify-center
                            w-10 h-10 rounded-full
                            bg-indigo-100 text-indigo-600 text-xl">
                    💡
                </div>

                <div class="text-gray-700 leading-relaxed">
                    <p class="font-semibold text-base mb-1">
                        Gợi ý cho bạn
                    </p>

                    @if($dueToday > 0)
                        Bạn có
                        <span class="font-semibold text-indigo-600">
                            {{ $dueToday }}
                        </span>
                        từ đến hạn ôn hôm nay.  
                        <span class="text-gray-500">Hãy tranh thủ ôn lại để giữ streak nhé 🔥</span>
                    @elseif($totalReviews < 20)
                        Bạn nên ôn lại thêm một số từ để ghi nhớ lâu hơn 📘
                    @else
                        Tiến độ rất tốt, tiếp tục duy trì nhé! 🚀
                    @endif
                </div>
            </div>
        </div>

        {{-- =========================
        UPDATE PROFILE INFO
        ========================= --}}
        <div class="bg-white shadow-sm rounded-2xl border border-gray-100 p-8">
            <h3 class="text-base font-semibold text-gray-800 mb-4">
                👤 Thông tin cá nhân
            </h3>

            @include('profile.partials.update-profile-information-form')
        </div>

        {{-- =========================
        UPDATE PASSWORD
        ========================= --}}
        <div class="bg-white shadow-sm rounded-2xl border border-gray-100 p-8">
            <h3 class="text-base font-semibold text-gray-800 mb-4">
                🔒 Đổi mật khẩu
            </h3>

            @include('profile.partials.update-password-form')
        </div>

        {{-- =========================
        DELETE USER
        ========================= --}}
        <div class="bg-white shadow-sm rounded-2xl border border-red-200 p-8">
            <h3 class="text-base font-semibold text-red-600 mb-4">
                ⚠️ Xoá tài khoản
            </h3>

            @include('profile.partials.delete-user-form')
        </div>

    </div>

@endsection
