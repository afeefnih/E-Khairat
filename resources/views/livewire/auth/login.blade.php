<x-guest-layout>
    <div class="min-h-screen bg-gray-900 py-12 flex items-center justify-center">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <!-- Logo centered at the top -->
            <div class="text-center mb-8">

                <div class="mx-auto w-24 h-24">
                <x-authentication-card-logo />
                </div>
                <h2 class="mt-4 text-3xl font-extrabold text-white">
                    Log Masuk
                </h2>
                <p class="mt-2 text-sm text-gray-400">
                    Sila masukkan maklumat akaun anda
                </p>
            </div>

            <div class="bg-gray-800 py-8 px-4 shadow-xl sm:rounded-xl sm:px-10">
                <x-validation-errors class="mb-4 rounded-lg bg-red-900/20 p-4 text-red-400" />

                @session('status')
                    <div class="mb-4 p-4 rounded-lg bg-green-900/20 text-green-400 text-sm font-medium">
                        {{ $value }}
                    </div>
                @endsession

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-label for="ic_number" value="{{ __('Kad Pengenalan') }}" class="block text-sm font-medium text-gray-300" />
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                </svg>
                            </div>
                            <x-input id="ic_number" class="block w-full pl-10 border-gray-600 focus:ring-purple-500 focus:border-purple-500 bg-gray-700 text-gray-300" type="text" name="ic_number" placeholder="Masukkan nombor KP tanpa dash (-)" required autofocus />
                        </div>
                    </div>

                    <div>
                        <x-label for="password" value="{{ __('Kata Laluan') }}" class="block text-sm font-medium text-gray-300" />
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <x-input id="password" class="block w-full pl-10 border-gray-600 focus:ring-purple-500 focus:border-purple-500 bg-gray-700 text-gray-300" type="password" name="password" placeholder="Masukkan kata laluan anda" required />
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="flex items-center">
                            <x-checkbox id="remember_me" name="remember" class="h-4 w-4 text-purple-600 border-gray-500 rounded focus:ring-purple-500 bg-gray-700" />
                            <span class="ml-2 text-sm text-gray-400">
                                {{ __('Ingat saya') }}
                            </span>
                        </label>

                        @if (Route::has('password.request'))
                            <div class="text-sm">
                                <a wire:navigate class="font-medium text-purple-400 hover:text-purple-300" href="{{ route('password.request') }}">
                                    {{ __('Lupa kata laluan?') }}
                                </a>
                            </div>
                        @endif
                    </div>

                    <div>
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            {{ __('Log Masuk') }}
                        </button>
                    </div>

                    <div class="mt-6 text-center">
                        <span class="text-sm text-gray-400">Belum mendaftar?</span>
                        <a wire:navigate href="{{ route('register') }}" class="ml-1 font-medium text-purple-400 hover:text-purple-300">
                            Daftar Sekarang
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
