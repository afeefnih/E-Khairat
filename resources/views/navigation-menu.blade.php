{{-- resources/views/livewire/navigation-menu.blade.php --}}

<nav x-data="{ open: false, sidebarOpen: false, profileOpen: false }" class="bg-white dark:bg-gray-900 fixed w-full z-20 top-0 start-0 border-b border-indigo-100 dark:border-indigo-900/40 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
        <div class="flex items-center">
            <!-- Sidebar Trigger with animation -->
            <button @click="sidebarOpen = !sidebarOpen"
                class="inline-flex items-center justify-center p-2 rounded-md md:block text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-indigo-600 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transition-transform duration-300"
                    :class="{ 'rotate-90': sidebarOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- Logo with improved spacing -->
            <a wire:navigate href="{{route('dashboard')}}" class="flex items-center space-x-3 rtl:space-x-reverse">
                <div class="h-10 w-10 bg-indigo-50 dark:bg-indigo-900/30 rounded-full p-1 flex items-center justify-center">
                    <img src="{{asset('images/logo.png')}}" class="h-8" alt="E-Khairat Logo">
                </div>
                <span class="self-center text-xl font-bold text-indigo-800 dark:text-white">E-Khairat</span>
            </a>
        </div>

        <!-- Center Navigation Links (Desktop) -->
        <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1">
            <ul class="flex flex-col p-4 md:p-0 mt-4 font-medium border border-indigo-100 rounded-lg bg-white md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-transparent dark:bg-gray-800 md:dark:bg-transparent dark:border-indigo-900/40">
                <li>
                    <a wire:navigate href="{{route('dashboard')}}"
                       class="block py-2 px-3 text-gray-700 rounded hover:bg-indigo-50 md:hover:bg-transparent md:hover:text-indigo-600 md:p-0 md:dark:hover:text-indigo-400 dark:text-white dark:hover:bg-indigo-900/30 dark:hover:text-white md:dark:hover:bg-transparent transition-colors duration-200
                              {{ request()->routeIs('dashboard') ? 'text-indigo-600 dark:text-indigo-400' : '' }}">
                        Dashboard
                    </a>
                </li>
                <li>
                    <a wire:navigate href="/maklumat-ahli"
                       class="block py-2 px-3 text-gray-700 rounded hover:bg-indigo-50 md:hover:bg-transparent md:hover:text-indigo-600 md:p-0 md:dark:hover:text-indigo-400 dark:text-white dark:hover:bg-indigo-900/30 dark:hover:text-white md:dark:hover:bg-transparent transition-colors duration-200
                              {{ request()->routeIs('maklumat-ahli') ? 'text-indigo-600 dark:text-indigo-400' : '' }}">
                        Maklumat Ahli
                    </a>
                </li>
            </ul>
        </div>

        <!-- Right Side Items -->
        <div class="flex md:order-2 space-x-3 md:space-x-3 rtl:space-x-reverse">
            <!-- Dark Mode Toggle -->
            <button @click="darkMode = !darkMode"
                    class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-600 rounded-lg hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-indigo-600 transition-colors duration-200">
                <template x-if="darkMode">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </template>
                <template x-if="!darkMode">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </template>
            </button>

            <!-- Settings Dropdown -->
            <div class="relative">
                <button @click="profileOpen = !profileOpen" type="button"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-200 transition-colors duration-200 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-indigo-600">
                    <div class="flex items-center">
                        <!-- User avatar -->
                        <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-300 font-semibold mr-2">
                            {{ Auth::user()->name[0] ?? 'U' }}
                        </div>
                        <div>
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="ms-2 -me-0.5 size-4 transition-transform duration-200"
                                :class="{ 'rotate-180': profileOpen }"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                    </div>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="profileOpen"
                        @click.away="profileOpen = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 z-50 mt-2 w-48 rounded-lg shadow-lg origin-top-right bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 dark:divide-gray-700">

                    <div class="py-1">
                        <div class="block px-4 py-2 text-xs text-gray-400 dark:text-gray-300 font-semibold">
                            {{ __('Manage Account') }}
                        </div>

                        <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 transition-colors duration-200">
                            <div class="flex items-center">
                                <svg class="mr-3 h-5 w-5 text-gray-400 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                {{ __('Tukar Password') }}
                            </div>
                        </a>
                    </div>

                    <div class="py-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <div class="flex items-center">
                                    <svg class="mr-3 h-5 w-5 text-gray-400 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    {{ __('Log Out') }}
                                </div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Mobile menu button -->
            <button @click="open = !open" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-600 rounded-lg md:hidden hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-indigo-600 ml-3" aria-controls="navbar-sticky" :aria-expanded="open ? 'true' : 'false'">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile menu -->
    <div :class="{'hidden': !open, 'block': open}" class="items-center justify-between w-full md:hidden" id="navbar-sticky">
        <ul class="flex flex-col p-4 mt-4 font-medium border border-indigo-100 rounded-lg bg-white md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-transparent dark:bg-gray-800 md:dark:bg-transparent dark:border-indigo-900/40">
            <li>
                <a wire:navigate href="{{route('dashboard')}}" class="block py-2 px-3 text-gray-700 rounded hover:bg-indigo-50 md:hover:bg-transparent md:hover:text-indigo-600 md:p-0 md:dark:hover:text-indigo-400 dark:text-white dark:hover:bg-indigo-900/30 dark:hover:text-white md:dark:hover:bg-transparent transition-colors duration-200">Dashboard</a>
            </li>
            <li>
                <a wire:navigate href="/maklumat-ahli" class="block py-2 px-3 text-gray-700 rounded hover:bg-indigo-50 md:hover:bg-transparent md:hover:text-indigo-600 md:p-0 md:dark:hover:text-indigo-400 dark:text-white dark:hover:bg-indigo-900/30 dark:hover:text-white md:dark:hover:bg-transparent transition-colors duration-200">Maklumat Ahli</a>
            </li>
            <li>
                <a href="{{ route('profile.show') }}" class="block py-2 px-3 text-gray-700 rounded hover:bg-indigo-50 md:hover:bg-transparent md:hover:text-indigo-600 md:p-0 md:dark:hover:text-indigo-400 dark:text-white dark:hover:bg-indigo-900/30 dark:hover:text-white md:dark:hover:bg-transparent transition-colors duration-200">Tukar Password</a>
            </li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left block py-2 px-3 text-gray-700 rounded hover:bg-indigo-50 md:hover:bg-transparent md:hover:text-indigo-600 md:p-0 md:dark:hover:text-indigo-400 dark:text-white dark:hover:bg-indigo-900/30 dark:hover:text-white md:dark:hover:bg-transparent transition-colors duration-200">Log Out</button>
                </form>
            </li>
        </ul>
    </div>

    <!-- Sidebar (Enhanced with improved styling and smooth transitions) -->
    <div
        x-show="sidebarOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 left-0 z-50 w-64 shadow-xl transform overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-700 bg-white dark:bg-gray-900"
    >
        <div class="p-5 flex flex-col h-full">
            <!-- Header with Close Button -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-indigo-800 dark:text-white">
                    <span class="flex items-center">
                        <!-- App Logo/Icon -->
                        <div class="h-10 w-10 bg-indigo-50 dark:bg-indigo-900/30 rounded-full p-1 flex items-center justify-center mr-2">
                            <img src="{{asset('images/logo.png')}}" class="h-8" alt="E-Khairat Logo">
                        </div>
                        E-Khairat
                    </span>
                </h2>
                <button
                    @click="sidebarOpen = false"
                    class="p-1.5 rounded-full hover:bg-indigo-50 text-gray-600 hover:text-indigo-600 dark:hover:bg-indigo-900/30 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors duration-200"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- User Profile Section -->
            <div class="mb-6 pb-6 border-b border-indigo-100 dark:border-indigo-900/40">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-300 font-bold">
                        {{ Auth::user()->name[0] ?? 'U' }}
                    </div>
                    <div class="ml-3">
                        <p class="font-medium text-gray-800 dark:text-white">
                            {{ Auth::user()->name ?? 'User' }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ Auth::user()->email ?? 'user@example.com' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-grow">
                <div class="space-y-1">
                    <!-- Dashboard Link -->
                    <a wire:navigate href="/dashboard"
                        class="flex items-center px-4 py-2.5 rounded-lg transition-colors duration-200 font-medium"
                        :class="window.location.pathname === '/dashboard' ?
                            'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400' :
                            'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 dark:text-gray-300 dark:hover:bg-indigo-900/30 dark:hover:text-indigo-400'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" :class="window.location.pathname === '/dashboard' ?
                            'text-indigo-600 dark:text-indigo-400' :
                            'text-gray-500 dark:text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>

                    <!-- Maklumat Ahli Link -->
                    <a wire:navigate href="/maklumat-ahli"
                        class="flex items-center px-4 py-2.5 rounded-lg transition-colors duration-200 font-medium"
                        :class="window.location.pathname === '/maklumat-ahli' ?
                            'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400' :
                            'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 dark:text-gray-300 dark:hover:bg-indigo-900/30 dark:hover:text-indigo-400'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" :class="window.location.pathname === '/maklumat-ahli' ?
                            'text-indigo-600 dark:text-indigo-400' :
                            'text-gray-500 dark:text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Maklumat Ahli
                    </a>
                </div>

                <!-- Section divider -->
                <div class="mt-6 pt-6 border-t border-indigo-100 dark:border-indigo-900/40">
                    <h3 class="px-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                        Pengaturan
                    </h3>
                </div>

                <!-- Settings Links -->
                <div class="mt-2 space-y-1">
                    <!-- Profile Settings Link -->
                    <a href="{{ route('profile.show') }}"
                        class="flex items-center px-4 py-2.5 rounded-lg transition-colors duration-200 font-medium"
                        :class="window.location.pathname === '/user/profile' ?
                            'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400' :
                            'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 dark:text-gray-300 dark:hover:bg-indigo-900/30 dark:hover:text-indigo-400'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" :class="window.location.pathname === '/user/profile' ?
                            'text-indigo-600 dark:text-indigo-400' :
                            'text-gray-500 dark:text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Tukar Password
                    </a>
                </div>
            </nav>

            <!-- Settings Section -->
            <div class="mt-auto pt-6 border-t border-indigo-100 dark:border-indigo-900/40">
                <div class="space-y-3">
                    <!-- Dark Mode Toggle with improved styling -->
                    <button
                        @click="darkMode = !darkMode"
                        class="w-full flex items-center px-4 py-2.5 rounded-lg transition-colors duration-200 font-medium text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 dark:text-gray-300 dark:hover:bg-indigo-900/30 dark:hover:text-indigo-400"
                    >
                        <template x-if="darkMode">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </template>
                        <template x-if="!darkMode">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </template>
                        <span x-text="darkMode ? 'Light Mode' : 'Dark Mode'"></span>
                    </button>

                    <!-- Logout Button with improved styling -->
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center px-4 py-2.5 rounded-lg transition-colors duration-200 font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-red-500 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Log Out
                        </button>
                    </form>
                </div>

                <!-- Version Info (Optional) -->
                <div class="mt-6 px-4 py-2">
                    <p class="text-xs text-gray-500 dark:text-gray-400">E-Khairat v1.0</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">© 2025 Masjid Taman Sutera</p>
                </div>
            </div>
        </div>
    </div>
</nav>


