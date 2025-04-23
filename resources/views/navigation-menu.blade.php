<nav x-data="{ open: false, sidebarOpen: false, profileOpen: false }" class="bg-white border-b border-gray-100 dark:bg-gray-900 dark:border-gray-700 sticky top-0 z-50 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Sidebar Trigger with animation -->
                <button @click="sidebarOpen = !sidebarOpen"
                    class="inline-flex items-center justify-center p-2 rounded-md md:block dark:text-gray-300 dark:hover:bg-gray-700 text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transition-transform duration-300"
                        :class="{ 'rotate-90': sidebarOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Logo with improved spacing -->
                <a wire:navigate href="{{route('dashboard')}}" class="flex items-center space-x-3 rtl:space-x-reverse ml-2">
                    <img src="{{asset('images/logo.png')}}" class="h-9" alt="E-Khairat Logo">
                    <span class="self-center text-xl font-semibold whitespace-nowrap dark:text-white text-gray-800">E-Khairat</span>
                </a>
            </div>

            <!-- Center Navigation Links (New) -->
            <div class="hidden md:flex md:items-center md:justify-center flex-1 px-4">
                <div class="flex space-x-4">
                    <a wire:navigate href="{{route('dashboard')}}"
                       class="px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200
                              {{ request()->routeIs('dashboard') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400' }}">
                        Dashboard
                    </a>
                    <a wire:navigate href="/maklumat-ahli"
                       class="px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200
                              {{ request()->routeIs('maklumat-ahli') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400' }}">
                        Maklumat Ahli
                    </a>

                </div>
            </div>

            <!-- Right Side Items -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Dark Mode Toggle (New in header) -->
                <button @click="darkMode = !darkMode"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 transition-all duration-200 mr-3">
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

                <!-- Settings Dropdown with improved design -->
                <div class="ms-3 relative">
                    <div>
                        <button @click="profileOpen = !profileOpen" type="button"
                            class="flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-600 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150 dark:text-gray-300 dark:bg-gray-800 dark:hover:text-gray-200 dark:hover:bg-gray-700 dark:focus:bg-gray-700 dark:active:bg-gray-700 group">
                            <div class="flex items-center">
                                <!-- User avatar (optional) -->
                                <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 dark:text-indigo-300 font-semibold mr-2">
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
                    </div>

                    <!-- Dropdown Menu -->
                    <div x-show="profileOpen"
                         @click.away="profileOpen = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 z-50 mt-2 w-48 rounded-md shadow-lg origin-top-right bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 dark:divide-gray-700">

                        <div class="py-1">
                            <div class="block px-4 py-2 text-xs text-gray-400 dark:text-gray-300 font-semibold">
                                {{ __('Manage Account') }}
                            </div>

                            <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
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
                                <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
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
            </div>


        </div>
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
        :class="darkMode ? 'bg-gray-900 text-white' : 'bg-white text-gray-800'"
        class="fixed inset-y-0 left-0 z-50 w-64 shadow-xl transform overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-700"
    >
        <div class="p-5 flex flex-col h-full">
            <!-- Header with Close Button -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold" :class="darkMode ? 'text-white' : 'text-gray-800'">
                    <span class="flex items-center">
                        <!-- App Logo/Icon -->
                        <img src="{{asset('images/logo.png')}}" class="h-8 mr-2" alt="E-Khairat Logo">
                        E-Khairat
                    </span>
                </h2>
                <button
                    @click="sidebarOpen = false"
                    class="p-1.5 rounded-full transition-colors duration-200"
                    :class="darkMode ? 'hover:bg-gray-700 text-gray-400 hover:text-white' : 'hover:bg-gray-100 text-gray-500 hover:text-gray-700'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- User Profile Section -->
            <div class="mb-6 pb-6 border-b" :class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold">
                        {{ Auth::user()->name[0] ?? 'U' }}
                    </div>
                    <div class="ml-3">
                        <p class="font-medium" :class="darkMode ? 'text-white' : 'text-gray-900'">
                            {{ Auth::user()->name ?? 'User' }}
                        </p>
                        <p class="text-sm" :class="darkMode ? 'text-gray-400' : 'text-gray-500'">
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
                        class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 font-medium"
                        :class="window.location.pathname === '/dashboard' ?
                            (darkMode ? 'bg-indigo-700 text-white' : 'bg-indigo-50 text-indigo-700') :
                            (darkMode ? 'text-gray-300 hover:bg-gray-800 hover:text-white' : 'text-gray-700 hover:bg-gray-100')"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" :class="window.location.pathname === '/dashboard' ?
                            (darkMode ? 'text-white' : 'text-indigo-600') :
                            (darkMode ? 'text-gray-400' : 'text-gray-500')" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>

                    <!-- Maklumat Ahli Link -->
                    <a wire:navigate href="/maklumat-ahli"
                        class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 font-medium"
                        :class="window.location.pathname === '/maklumat-ahli' ?
                            (darkMode ? 'bg-indigo-700 text-white' : 'bg-indigo-50 text-indigo-700') :
                            (darkMode ? 'text-gray-300 hover:bg-gray-800 hover:text-white' : 'text-gray-700 hover:bg-gray-100')"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" :class="window.location.pathname === '/maklumat-ahli' ?
                            (darkMode ? 'text-white' : 'text-indigo-600') :
                            (darkMode ? 'text-gray-400' : 'text-gray-500')" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Maklumat Ahli
                    </a>


                </div>

                <!-- Section divider -->
                <div class="mt-6 pt-6 border-t" :class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                    <h3 class="px-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                        Pengaturan
                    </h3>
                </div>

                <!-- Settings Links -->
                <div class="mt-2 space-y-1">
                    <!-- Profile Settings Link -->
                    <a href="{{ route('profile.show') }}"
                        class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 font-medium"
                        :class="window.location.pathname === '/user/profile' ?
                            (darkMode ? 'bg-indigo-700 text-white' : 'bg-indigo-50 text-indigo-700') :
                            (darkMode ? 'text-gray-300 hover:bg-gray-800 hover:text-white' : 'text-gray-700 hover:bg-gray-100')"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" :class="window.location.pathname === '/user/profile' ?
                            (darkMode ? 'text-white' : 'text-indigo-600') :
                            (darkMode ? 'text-gray-400' : 'text-gray-500')" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Tukar Password
                    </a>
                </div>
            </nav>

            <!-- Settings Section -->
            <div class="mt-auto pt-6 border-t" :class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                <div class="space-y-3">
                    <!-- Dark Mode Toggle with improved styling -->
                    <button
                        @click="darkMode = !darkMode"
                        class="w-full flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 font-medium"
                        :class="darkMode ? 'text-gray-300 hover:bg-gray-800 hover:text-white' : 'text-gray-700 hover:bg-gray-100'"
                    >
                        <template x-if="darkMode">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-yellow-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                            class="w-full flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20"
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
