<div>
    <x-validation-errors class="mb-4" />

    <form action="{{ route('payments.registration') }}" method="POST">
        @csrf

        <div class="space-y-8">
            <!-- User Information Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 py-4 px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="bg-white dark:bg-gray-900 rounded-full p-2 shadow-md mr-3">
                                <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <h2 class="text-xl font-semibold text-white">Maklumat Ahli</h2>
                        </div>
                        <button type="button" wire:click="backToRegister"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-indigo-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Kemaskini Maklumat
                        </button>
                    </div>
                </div>
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="space-y-1">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Nama</p>
                            <p class="text-base font-medium text-gray-900 dark:text-white">{{ $user_data['name'] }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm text-gray-500 dark:text-gray-400">No. KP</p>
                            <p class="text-base font-medium text-gray-900 dark:text-white">{{ $user_data['ic_number'] }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Emel</p>
                            <p class="text-base font-medium text-gray-900 dark:text-white">{{ $user_data['email'] }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Telefon</p>
                            <p class="text-base font-medium text-gray-900 dark:text-white">{{ $user_data['phone_number'] }}</p>
                        </div>
                        <div class="space-y-1 md:col-span-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Alamat</p>
                            <p class="text-base font-medium text-gray-900 dark:text-white">{{ $user_data['address'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dependents Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 py-4 px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="bg-white dark:bg-gray-900 rounded-full p-2 shadow-md mr-3">
                                <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <h2 class="text-xl font-semibold text-white">Maklumat Tanggungan</h2>
                        </div>
                        <button type="button" wire:navigate href="/register/dependent"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-indigo-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tambah Tanggungan
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Nama
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Pertalian
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    No. KP
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Umur
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($dependents as $index => $dependent)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $dependent['full_name'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        {{ $dependent['relationship'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        {{ $dependent['ic_number'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        {{ $dependent['age'] }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                            <p>Tiada tanggungan yang didaftarkan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Fee Summary Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 py-4 px-6">
                    <div class="flex items-center">
                        <div class="bg-white dark:bg-gray-900 rounded-full p-2 shadow-md mr-3">
                            <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-semibold text-white">Maklumat Yuran</h2>
                    </div>
                </div>
                <div class="px-6 py-6">
                    <div class="flex justify-between items-center">
                        <div class="space-y-1">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Yuran Pendaftaran</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-white">Pembayaran Sekali Sahaja</p>
                        </div>
                        <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">RM {{ number_format($amount, 2) }}</span>
                    </div>

                    <div class="mt-4 bg-indigo-50 dark:bg-indigo-900/20 border-l-4 border-indigo-500 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-indigo-700 dark:text-indigo-300">
                                    Pembayaran akan diproses melalui ToyyibPay. Anda akan diarahkan ke laman pembayaran yang selamat setelah mengesahkan maklumat.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Terms and Privacy Policy -->
        <div class="mt-8">
            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <input type="checkbox" wire:model="terms" name="terms" id="terms" required
                                class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:checked:bg-indigo-600" />
                        </div>
                        <div class="text-sm">
                            <label for="terms" class="font-medium text-gray-700 dark:text-gray-300">
                                {!! __('Saya setuju dengan :Terma dan Syarat ', [
                                    'Terma dan Syarat' =>
                                        '<a target="_blank" href="' .
                                        route('terms') .
                                        '" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">' .
                                        __('Terma dan Syarat') .
                                        '</a>',
                                ]) !!}
                            </label>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Submit Button -->
        <div class="flex justify-between mt-8">
            <a wire:navigate href="{{ route('register.dependent') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
            <button type="submit" class="inline-flex items-center px-8 py-3 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-700 dark:hover:bg-indigo-800 transition-colors duration-200">
                {{ __('Teruskan Pembayaran') }}
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </button>
        </div>
    </form>
</div>
