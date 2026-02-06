<nav x-data="{ open: false }" class="bg-white/80 backdrop-blur border-b border-gray-200 sticky top-0 z-50">

    <div class="max-w-7xl mx-auto px-6">
        <div class="flex justify-between h-16 items-center">

            <!-- LEFT -->
            <div class="flex items-center gap-10">

                <!-- BRAND -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-500
                                flex items-center justify-center text-white shadow">
                        🇰🇷
                    </div>
                    <span class="font-bold text-lg tracking-wide text-gray-800
                                 group-hover:text-indigo-600 transition">
                        Vocab Korean
                    </span>
                </a>

                <!-- MENU -->
                <div class="hidden sm:flex items-center gap-1">

                    @php
                        $base = 'px-4 py-2 rounded-lg text-sm font-medium transition';
                        $active = 'bg-indigo-50 text-indigo-600';
                        $idle = 'text-gray-600 hover:bg-gray-100 hover:text-gray-900';
                    @endphp

                    <a href="{{ route('dashboard') }}"
                        class="{{ $base }} {{ request()->routeIs('dashboard') ? $active : $idle }}">
                        📊 Dashboard
                    </a>

                    <a href="{{ url('/topics') }}" class="{{ $base }} {{ request()->is('topics*') ? $active : $idle }}">
                        📘 Chủ đề
                    </a>

                    <a href="{{ url('/review') }}" class="{{ $base }} {{ request()->is('review*') ? $active : $idle }}">
                        📚 Ôn tập
                    </a>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="hidden sm:flex items-center gap-3">

                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-3 bg-gray-100 hover:bg-gray-200
                           px-3 py-2 rounded-full transition shadow-sm">

                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500
                               flex items-center justify-center text-white font-bold text-sm">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>

                                <span class="text-sm font-semibold text-gray-700">
                                    {{ Auth::user()->name }}
                                </span>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                👤 Hồ sơ cá nhân
                            </x-dropdown-link>

                            <div class="border-t border-gray-100 my-1"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    🚪 Đăng xuất
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endauth

                @guest
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-600 hover:text-indigo-600">
                            Đăng nhập
                        </a>

                        <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg
                      text-sm font-semibold hover:bg-indigo-700 transition">
                            Đăng ký
                        </a>
                @endguest

            </div>

            <!-- MOBILE BUTTON -->
            <div class="sm:hidden">
                <button @click="open = !open" class="p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                    ☰
                </button>
            </div>

        </div>
    </div>

    <!-- MOBILE MENU -->
    <div x-show="open" x-transition class="sm:hidden bg-white border-t border-gray-200 px-4 py-4 space-y-2">

        <a href="{{ route('dashboard') }}" class="block px-4 py-3 rounded-lg hover:bg-gray-100">
            📊 Dashboard
        </a>
        <a href="{{ url('/topics') }}" class="block px-4 py-3 rounded-lg hover:bg-gray-100">
            📘 Chủ đề
        </a>
        <a href="{{ url('/review') }}" class="block px-4 py-3 rounded-lg hover:bg-gray-100">
            📚 Ôn tập
        </a>
        <a href="{{ route('profile.edit') }}" class="block px-4 py-3 rounded-lg hover:bg-gray-100">
            👤 Hồ sơ
        </a>
    </div>
</nav>