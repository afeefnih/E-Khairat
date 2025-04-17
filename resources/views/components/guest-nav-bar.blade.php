<nav class="bg-white dark:bg-gray-900 fixed w-full z-20 top-0 start-0 border-b border-indigo-100 dark:border-indigo-900/40 shadow-sm" x-data="{ open: false }">
    <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
        <a wire:navigate href="/" class="flex items-center space-x-3 rtl:space-x-reverse">
            <div class="h-10 w-10 bg-indigo-50 dark:bg-indigo-900/30 rounded-full p-1 flex items-center justify-center">
                <img src="{{asset('images/logo.png')}}" class="h-8" alt="E-Khairat Logo">
            </div>
            <span class="self-center text-xl font-bold text-indigo-800 dark:text-white">E-Khairat</span>
        </a>
        <div class="flex md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
            <a wire:navigate href="/register" class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors duration-200 dark:bg-indigo-700 dark:hover:bg-indigo-800 dark:focus:ring-indigo-800 shadow-sm">
                Pendaftaran
            </a>
            <button @click="open = !open" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-600 rounded-lg md:hidden hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-indigo-600 ml-3" aria-controls="navbar-sticky" :aria-expanded="open ? 'true' : 'false'">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
                </svg>
            </button>
        </div>
        <div :class="{'hidden': !open, 'block': open}" class="items-center justify-between w-full md:flex md:w-auto md:order-1" id="navbar-sticky">
            <ul class="flex flex-col p-4 md:p-0 mt-4 font-medium border border-indigo-100 rounded-lg bg-white md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-transparent dark:bg-gray-800 md:dark:bg-transparent dark:border-indigo-900/40">
                <li>
                    <a wire:navigate href="/" class="block py-2 px-3 text-gray-700 rounded hover:bg-indigo-50 md:hover:bg-transparent md:hover:text-indigo-600 md:p-0 md:dark:hover:text-indigo-400 dark:text-white dark:hover:bg-indigo-900/30 dark:hover:text-white md:dark:hover:bg-transparent transition-colors duration-200">Utama</a>
                </li>
                <li>
                    <a wire:navigate href="/terms" class="block py-2 px-3 text-gray-700 rounded hover:bg-indigo-50 md:hover:bg-transparent md:hover:text-indigo-600 md:p-0 md:dark:hover:text-indigo-400 dark:text-white dark:hover:bg-indigo-900/30 dark:hover:text-white md:dark:hover:bg-transparent transition-colors duration-200">Syarat</a>
                </li>
                <li>
                    <a wire:navigate href="/infaq" class="block py-2 px-3 text-gray-700 rounded hover:bg-indigo-50 md:hover:bg-transparent md:hover:text-indigo-600 md:p-0 md:dark:hover:text-indigo-400 dark:text-white dark:hover:bg-indigo-900/30 dark:hover:text-white md:dark:hover:bg-transparent transition-colors duration-200">Infaq</a>
                </li>
                <li>
                    <a href="#" class="block py-2 px-3 text-gray-700 rounded hover:bg-indigo-50 md:hover:bg-transparent md:hover:text-indigo-600 md:p-0 md:dark:hover:text-indigo-400 dark:text-white dark:hover:bg-indigo-900/30 dark:hover:text-white md:dark:hover:bg-transparent transition-colors duration-200">Contact</a>
                </li>
                <li>
                    <x-theme-button/>
                </li>
            </ul>
        </div>
    </div>
</nav>
