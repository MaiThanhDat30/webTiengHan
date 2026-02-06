@extends('layouts.app')

@section('content')
<div class="bg-gradient-to-b from-indigo-50 to-white">
    <!-- Hero -->
    <section class="max-w-6xl mx-auto px-6 py-20 text-center">
        <h1 class="text-4xl md:text-5xl font-extrabold text-indigo-700 mb-6">
            Nền tảng học từ vựng tiếng Hàn cá nhân hóa
        </h1>
        <p class="text-lg text-gray-700 max-w-3xl mx-auto mb-8">
            Ứng dụng mô hình <b>Spaced Repetition</b> kết hợp <b>phân tích dữ liệu người học</b>
            giúp ghi nhớ từ vựng lâu dài, hiệu quả và phù hợp với từng mục tiêu học tập.
        </p>
        <div class="flex justify-center gap-4">
            <a href="{{ route('register') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700">
                Bắt đầu học ngay
            </a>
            <a href="{{ route('login') }}" class="px-6 py-3 border border-indigo-600 text-indigo-600 rounded-xl font-semibold hover:bg-indigo-50">
                Đăng nhập
            </a>
        </div>
    </section>

    <!-- Giới thiệu dự án -->
    <section class="max-w-6xl mx-auto px-6 py-16">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">1. Giới thiệu dự án</h2>
        <p class="text-gray-700 leading-relaxed">
            Trong quá trình học tiếng Hàn, từ vựng đóng vai trò nền tảng quyết định khả năng nghe, nói,
            đọc và viết. Tuy nhiên, nhiều người học gặp tình trạng “học trước quên sau”, đặc biệt khi
            chuẩn bị cho kỳ thi TOPIK hoặc sử dụng tiếng Hàn trong môi trường học tập và làm việc.
        </p>
        <p class="text-gray-700 leading-relaxed mt-4">
            Dự án này đề xuất mô hình học từ vựng tiếng Hàn <b>cá nhân hóa</b> dựa trên nguyên lý
            <b>lặp lại ngắt quãng (Spaced Repetition)</b> và phân tích dữ liệu người học,
            nhằm tối ưu hóa khả năng ghi nhớ và phù hợp với năng lực từng cá nhân.
        </p>
    </section>

    <!-- Thực trạng & Vấn đề -->
    <section class="bg-white max-w-6xl mx-auto px-6 py-16 grid md:grid-cols-2 gap-10">
        <div>
            <h3 class="text-2xl font-semibold text-indigo-600 mb-4">Thực trạng hiện nay</h3>
            <ul class="list-disc list-inside text-gray-700 space-y-2">
                <li>Nhu cầu học tiếng Hàn tăng mạnh do làn sóng văn hóa Hàn Quốc.</li>
                <li>Du học, lao động và làm việc tại doanh nghiệp Hàn Quốc.</li>
                <li>Kỳ thi TOPIK trở thành tiêu chuẩn đánh giá năng lực.</li>
                <li>Phương pháp học từ vựng còn truyền thống, thiếu cá nhân hóa.</li>
            </ul>
        </div>
        <div>
            <h3 class="text-2xl font-semibold text-red-500 mb-4">Vấn đề tồn tại</h3>
            <ul class="list-disc list-inside text-gray-700 space-y-2">
                <li>Không nhớ lâu từ vựng đã học.</li>
                <li>Không xác định được từ vựng quan trọng cho mục tiêu cá nhân.</li>
                <li>Học không phù hợp với trình độ.</li>
                <li>Thiếu hệ thống theo dõi tiến độ học tập.</li>
            </ul>
        </div>
    </section>

    <!-- Mục tiêu -->
    <section class="max-w-6xl mx-auto px-6 py-16">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Mục tiêu của dự án</h2>
        <div class="grid md:grid-cols-2 gap-8">
            <div class="p-6 rounded-xl bg-indigo-50">
                <h4 class="font-semibold text-indigo-700 mb-2">Mục tiêu tổng quát</h4>
                <p class="text-gray-700">
                    Nâng cao hiệu quả ghi nhớ từ vựng tiếng Hàn thông qua mô hình học cá nhân hóa
                    dựa trên Spaced Repetition và dữ liệu người học.
                </p>
            </div>
            <div class="p-6 rounded-xl bg-white shadow">
                <h4 class="font-semibold text-gray-800 mb-2">Mục tiêu cụ thể</h4>
                <ul class="list-disc list-inside text-gray-700 space-y-1">
                    <li>Phân loại từ vựng theo TOPIK, giao tiếp, du học, chuyên ngành.</li>
                    <li>Cá nhân hóa lộ trình học từ vựng.</li>
                    <li>Phân tích dữ liệu học tập của người học.</li>
                    <li>Đánh giá hiệu quả thông qua khảo sát sinh viên.</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Mô hình đề xuất -->
    <section class="bg-gray-50 max-w-6xl mx-auto px-6 py-16">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Mô hình học từ vựng đề xuất</h2>
        <div class="grid md:grid-cols-4 gap-6">
            <div class="bg-white p-5 rounded-xl shadow">
                <h4 class="font-semibold text-indigo-600 mb-2">Kho từ vựng</h4>
                <p class="text-sm text-gray-700">Phân loại theo TOPIK, chủ đề và độ khó.</p>
            </div>
            <div class="bg-white p-5 rounded-xl shadow">
                <h4 class="font-semibold text-indigo-600 mb-2">Hồ sơ người học</h4>
                <p class="text-sm text-gray-700">Trình độ, mục tiêu, kết quả và tần suất ôn tập.</p>
            </div>
            <div class="bg-white p-5 rounded-xl shadow">
                <h4 class="font-semibold text-indigo-600 mb-2">Spaced Repetition</h4>
                <p class="text-sm text-gray-700">Tự động đề xuất lịch ôn tập tối ưu.</p>
            </div>
            <div class="bg-white p-5 rounded-xl shadow">
                <h4 class="font-semibold text-indigo-600 mb-2">Cá nhân hóa</h4>
                <p class="text-sm text-gray-700">Điều chỉnh nội dung học theo hành vi người học.</p>
            </div>
        </div>
    </section>
</div>
@endsection