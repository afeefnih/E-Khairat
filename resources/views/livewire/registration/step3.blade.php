<div>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">Invois dan Pembayaran</h1>
                <div class="h-1 w-24 bg-blue-600 mx-auto rounded-full"></div>
            </div>

            <!-- Main Content -->
            <div class="space-y-8">
                <!-- User Information Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                        <div class="flex items-center">
                            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <h2 class="ml-3 text-xl font-semibold text-gray-900 dark:text-white">Maklumat Ahli</h2>
                        </div>
                    </div>
                    <div class="px-6 py-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div class="space-y-1">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Nama</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $user_data['name'] }}
                                </p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm text-gray-500 dark:text-gray-400">No. KP</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">
                                    {{ $user_data['ic_number'] }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Emel</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $user_data['email'] }}
                                </p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Telefon</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">
                                    {{ $user_data['phone_number'] }}</p>
                            </div>
                            <div class="space-y-1 md:col-span-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Alamat</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">
                                    {{ $user_data['address'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dependents Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <h2 class="ml-3 text-xl font-semibold text-gray-900 dark:text-white">Maklumat Tangungan
                                </h2>
                            </div>

                            <a wire:navigate
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
                                href="{{ route('register.dependent') }}">Kemaskini Tanguangan</a>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700">
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Nama</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Pertalian</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        No. KP</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Umur</th>

                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($dependents as $index => $dependent)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $dependent['full_name'] }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                            {{ $dependent['relationship'] }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                            {{ $dependent['ic_number'] }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                            {{ $dependent['age'] }}
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5"
                                            class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                            Tiada tanggungan yang didaftarkan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Fee Summary Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                        <div class="flex items-center">
                            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h2 class="ml-3 text-xl font-semibold text-gray-900 dark:text-white">Makluman Yuran</h2>
                        </div>
                    </div>
                    <div class="px-6 py-6">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-medium text-gray-900 dark:text-white">Yuran Pendaftaran</span>
                            <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">RM
                                {{ number_format($amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Terms and Privacy Policy -->
                @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <x-checkbox wire:model="terms" name="terms" id="terms" required
                                    class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:checked:bg-blue-600" />
                            </div>
                            <div class="text-sm">
                                <label for="terms" class="font-medium text-gray-700 dark:text-gray-300">
                                    {!! __('Saya setuju dengan :Terma dan Syarat ', [
                                        'Terma dan Syarat' =>
                                            '<a target="_blank" href="' .
                                            route('terms.show') .
                                            '" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">' .
                                            __('Terma dan Syarat') .
                                            '</a>',
                                    ]) !!}
                                </label>
                            </div>
                        </div>
                    </div>
                @endif</div>
