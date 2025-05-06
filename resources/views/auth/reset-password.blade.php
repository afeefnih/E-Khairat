<x-guest-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-indigo-950 py-12 flex items-center justify-center">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="text-center mb-8">
                <div class="mx-auto w-24 h-24 bg-white dark:bg-gray-800 rounded-full p-1 shadow-lg flex items-center justify-center">
                    <a href="/">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-20 w-20 object-contain">
                    </a>
                </div>
                <h2 class="mt-4 text-3xl font-extrabold text-gray-900 dark:text-white">
                    Tetapkan Semula Kata Laluan
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Sila masukkan kata laluan baharu anda
                </p>
            </div>
            <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow-xl sm:rounded-xl sm:px-10">
                <x-validation-errors class="mb-4 rounded-lg bg-red-50 dark:bg-red-900/20 p-4 text-red-600 dark:text-red-400" />
                <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                    <div>
                        <x-label for="email" value="{{ __('Emel') }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300" />
                        <x-input id="email" class="block w-full border-gray-300 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-300 rounded-lg" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
                    </div>
                    <div>
                        <x-label for="password" value="{{ __('Kata Laluan Baharu') }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300" />
                        <div class="mt-1 relative rounded-md shadow-sm" x-data="{ show: false }">
                            <x-input id="password" x-bind:type="show ? 'text' : 'password'" class="block w-full pr-10 border-gray-300 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-300 rounded-lg" name="password" required autocomplete="new-password" placeholder="Masukkan kata laluan baharu" />
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 focus:outline-none" tabindex="-1">
                                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-5.523 0-10-4.477-10-10 0-1.657.402-3.221 1.125-4.575m1.664-2.336A9.956 9.956 0 0112 3c5.523 0 10 4.477 10 10 0 1.657-.402 3.221-1.125 4.575m-1.664 2.336A9.956 9.956 0 0112 21c-5.523 0-10-4.477-10-10 0-1.657.402-3.221 1.125-4.575" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <x-label for="password_confirmation" value="{{ __('Sahkan Kata Laluan') }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300" />
                        <div class="mt-1 relative rounded-md shadow-sm" x-data="{ show: false }">
                            <x-input id="password_confirmation" x-bind:type="show ? 'text' : 'password'" class="block w-full pr-10 border-gray-300 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-300 rounded-lg" name="password_confirmation" required autocomplete="new-password" placeholder="Sahkan kata laluan baharu" />
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 focus:outline-none" tabindex="-1">
                                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-5.523 0-10-4.477-10-10 0-1.657.402-3.221 1.125-4.575m1.664-2.336A9.956 9.956 0 0112 3c5.523 0 10 4.477 10 10 0 1.657-.402 3.221-1.125 4.575m-1.664 2.336A9.956 9.956 0 0112 21c-5.523 0-10-4.477-10-10 0-1.657.402-3.221 1.125-4.575" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-colors duration-200">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Tetapkan Semula Kata Laluan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
