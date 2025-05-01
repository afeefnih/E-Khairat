<x-form-section submit="updatePassword">
    <x-slot name="title">
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 dark:text-indigo-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
            </svg>
            <span class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Kemas Kini Kata Laluan') }}</span>
        </div>
    </x-slot>

    <x-slot name="description">
        <span class="text-sm text-gray-600 dark:text-gray-400">
            {{ __('Pastikan akaun anda menggunakan kata laluan yang selamat dan unik untuk keselamatan maksimum.') }}
        </span>
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6 sm:col-span-4 space-y-6">
            <div>
                <x-label for="current_password" value="{{ __('Kata Laluan Semasa') }}" class="inline-flex text-sm font-medium text-gray-700 dark:text-gray-300" />
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <x-input id="current_password" type="password"
                        class="block w-full pl-10 border-gray-300 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-300 rounded-lg shadow-sm"
                        wire:model="state.current_password"
                        autocomplete="current-password" />
                </div>
                <x-input-error for="current_password" class="mt-2" />
            </div>

            <div>
                <x-label for="password" value="{{ __('Kata Laluan Baharu') }}" class="inline-flex text-sm font-medium text-gray-700 dark:text-gray-300" />
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <x-input id="password" type="password"
                        class="block w-full pl-10 border-gray-300 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-300 rounded-lg shadow-sm"
                        wire:model="state.password"
                        autocomplete="new-password" />
                </div>
                <x-input-error for="password" class="mt-2" />
            </div>

            <div>
                <x-label for="password_confirmation" value="{{ __('Sahkan Kata Laluan') }}" class="inline-flex text-sm font-medium text-gray-700 dark:text-gray-300" />
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <x-input id="password_confirmation" type="password"
                        class="block w-full pl-10 border-gray-300 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-300 rounded-lg shadow-sm"
                        wire:model="state.password_confirmation"
                        autocomplete="new-password" />
                </div>
                <x-input-error for="password_confirmation" class="mt-2" />
            </div>
        </div>
    </x-slot>

    <x-slot name="actions">
        <div class="flex items-center gap-4">
            <x-action-message class="text-green-600 dark:text-green-400" on="saved">
                {{ __('Disimpan.') }}
            </x-action-message>

            <x-button class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 active:bg-indigo-900 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
                {{ __('Simpan') }}
            </x-button>
        </div>
    </x-slot>
</x-form-section>
