<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Vocab Korean') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400;500;600&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;600&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-800">

    <!-- NAVIGATION -->
    @include('layouts.navigation')

    <!-- PAGE HEADER (SỬA: dùng section/yield) -->
    @hasSection('header')
        <header class="max-w-7xl mx-auto px-6 mt-8 mb-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100
                        px-6 py-4 flex items-center justify-between">
                @yield('header')
            </div>
        </header>
    @endif

    <!-- PAGE CONTENT -->
    <main class="max-w-7xl mx-auto px-6 pb-16">
        @yield('content')
    </main>

    <!-- ⭐ QUAN TRỌNG: LOAD JS TỪ VIEW -->
    @yield('scripts')

</body>
</html>
