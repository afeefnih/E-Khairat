<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-indigo-950 py-12 mt-10">
    <div class="container max-w-4xl mx-auto px-4">
        <!-- Page Header -->
        <div class="text-center mb-10">
            <div class="inline-block p-2 bg-white dark:bg-gray-800 rounded-full shadow-md mb-4">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-16 h-16 object-contain">
            </div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                Pendaftaran Ahli Khairat
            </h1>
            <p class="mt-3 text-lg text-gray-600 dark:text-gray-400">
                Sila lengkapkan maklumat anda untuk mendaftar
            </p>
        </div>

        <!-- Progress Bar -->
        <div class="max-w-xl mx-auto mb-10 px-4">
            <div class="flex items-center justify-between mb-2 text-sm font-medium text-indigo-700 dark:text-indigo-300">
                <span>Langkah 1 dari 3</span>
                <span>Pendaftaran Pengguna</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-indigo-700 h-2 rounded-full" style="width: 33%"></div>
            </div>
        </div>

        <!-- Card Container -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl overflow-hidden">
            <!-- Form Content -->
            <div class="p-6 sm:p-8 md:p-10">
                <x-validation-errors class="mb-6 rounded-lg bg-red-50 dark:bg-red-900/30 p-4 text-sm text-red-700 dark:text-red-300" />

                <form wire:submit.prevent="submit" class="space-y-8">
                    @csrf

                    <!-- Personal Information Section -->
                    <fieldset class="space-y-6 border-l-4 border-indigo-500 pl-6">
                        <legend class="text-lg font-semibold text-gray-900 dark:text-white flex items-center mb-4">
                            <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Maklumat Peribadi
                        </legend>

                        <!-- Full Name (Nama Penuh) -->
                        <div class="relative">
                            <input type="text" wire:model="name" id="name"
                                   class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-indigo-500 focus:outline-none focus:ring-0 focus:border-indigo-600 peer"
                                   placeholder=" " autofocus autocomplete="name" />
                            <label for="name"
                                   class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-800 px-2 peer-focus:px-2 peer-focus:text-indigo-600 peer-focus:dark:text-indigo-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">
                                {{ __('Nama Penuh') }} <span class="text-red-500">*</span>
                            </label>
                        </div>

                        <!-- Two Column Layout -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Kad Pengenalan (IC Number) -->
                            <div class="relative">
                                <input type="text" wire:model.live="ic_number" id="ic_number"
                                       maxlength="12" pattern="\d{12}" inputmode="numeric"
                                       class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-indigo-500 focus:outline-none focus:ring-0 focus:border-indigo-600 peer"
                                       placeholder=" " />
                                <label for="ic_number"
                                       class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-800 px-2 peer-focus:px-2 peer-focus:text-indigo-600 peer-focus:dark:text-indigo-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">
                                    {{ __('Kad Pengenalan') }} <span class="text-red-500">*</span>
                                </label>
                            </div>

                            <!-- Age (Umur) -->
                            <div class="relative">
                                <input type="number" wire:model="age" id="age" readonly
                                       class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-500 bg-gray-50 rounded-lg border border-gray-300 appearance-none dark:text-gray-400 dark:bg-gray-700 dark:border-gray-600 cursor-not-allowed peer"
                                       placeholder=" " />
                                <label for="age"
                                       class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-gray-50 dark:bg-gray-700 px-2 peer-focus:px-2 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">
                                    {{ __('Umur') }} <span class="text-red-500">*</span>
                                </label>
                            </div>
                        </div>
                    </fieldset>

                    <!-- Contact Information Section -->
                    <fieldset class="space-y-6 border-l-4 border-indigo-500 pl-6">
                        <legend class="text-lg font-semibold text-gray-900 dark:text-white flex items-center mb-4">
                            <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            Maklumat Perhubungan
                        </legend>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Phone Number (No Telefon) -->
                            <div class="relative">
                                <input type="text" wire:model="phone_number" id="phone_number"
                                       class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-indigo-500 focus:outline-none focus:ring-0 focus:border-indigo-600 peer"
                                       placeholder=" " />
                                <label for="phone_number"
                                       class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-800 px-2 peer-focus:px-2 peer-focus:text-indigo-600 peer-focus:dark:text-indigo-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">
                                    {{ __('No Telefon') }} <span class="text-red-500">*</span>
                                </label>
                            </div>

                            <!-- Home Phone (No Telefon Rumah) -->
                            <div class="relative">
                                <input type="text" wire:model="home_phone" id="home_phone"
                                       class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-indigo-500 focus:outline-none focus:ring-0 focus:border-indigo-600 peer"
                                       placeholder=" " />
                                <label for="home_phone"
                                       class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-800 px-2 peer-focus:px-2 peer-focus:text-indigo-600 peer-focus:dark:text-indigo-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">
                                    {{ __('No Telefon Rumah') }}
                                </label>
                            </div>
                        </div>

                        <!-- Address (Alamat) -->
                        <div class="relative">
                            <input type="text" wire:model="address" id="address"
                                   class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-indigo-500 focus:outline-none focus:ring-0 focus:border-indigo-600 peer"
                                   placeholder=" " />
                            <label for="address"
                                   class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-800 px-2 peer-focus:px-2 peer-focus:text-indigo-600 peer-focus:dark:text-indigo-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">
                                {{ __('Alamat') }} <span class="text-red-500">*</span>
                            </label>
                        </div>

                        <!-- Residence Status (Status Permastautin) -->
                        <div>
                            <label for="residence_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                {{ __('Status Permastautin') }} <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="residence_status" id="residence_status" wire:model="residence_status"
                                    class="appearance-none block w-full px-3.5 py-2.5 text-sm text-gray-900 bg-white dark:bg-gray-800 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:text-white dark:border-gray-600 dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
                                >
                                    <option value="" disabled selected>Pilih Status Permastautin</option>
                                    <option value="kekal">Kekal</option>
                                    <option value="sewa">Sewa</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-700 dark:text-gray-300">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                </div>
                            </div>
                        </div>

                        <!-- Email (optional) -->
                        <div class="relative">
                            <input type="email" wire:model="email" name="email" id="email"
                                   class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-indigo-500 focus:outline-none focus:ring-0 focus:border-indigo-600 peer"
                                   placeholder=" " />
                            <label for="email"
                                   class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-800 px-2 peer-focus:px-2 peer-focus:text-indigo-600 peer-focus:dark:text-indigo-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">
                                {{ __('Email (Optional)') }}
                            </label>
                        </div>
                    </fieldset>

                    <!-- Account Setup Section -->
                    <fieldset class="space-y-6 border-l-4 border-indigo-500 pl-6">
                        <legend class="text-lg font-semibold text-gray-900 dark:text-white flex items-center mb-4">
                            <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            Maklumat Akaun
                        </legend>

                        <!-- Password -->
                        <div class="relative">
                            <input type="password" wire:model="password" name="password" id="password"
                                   class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-indigo-500 focus:outline-none focus:ring-0 focus:border-indigo-600 peer"
                                   placeholder=" " autocomplete="new-password" />
                            <label for="password"
                                   class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-800 px-2 peer-focus:px-2 peer-focus:text-indigo-600 peer-focus:dark:text-indigo-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">
                                {{ __('Kata Laluan') }} <span class="text-red-500">*</span>
                            </label>
                        </div>

                        <!-- Confirm Password -->
                        <div class="relative">
                            <input type="password" wire:model="password_confirmation" name="password_confirmation" id="password_confirmation"
                                   class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-indigo-500 focus:outline-none focus:ring-0 focus:border-indigo-600 peer"
                                   placeholder=" " autocomplete="new-password" />
                            <label for="password_confirmation"
                                   class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-800 px-2 peer-focus:px-2 peer-focus:text-indigo-600 peer-focus:dark:text-indigo-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">
                                {{ __('Sahkan Kata Laluan') }} <span class="text-red-500">*</span>
                            </label>
                        </div>
                    </fieldset>

                    <!-- Submit Button -->
                    <div class="flex flex-col sm:flex-row items-center justify-between pt-6 gap-4 border-t border-gray-200 dark:border-gray-700">
                        <a wire:navigate class="text-sm font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 flex items-center transition-colors" href="{{ route('login') }}">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path></svg>
                            {{ __('Sudah Daftar?') }}
                        </a>

                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 focus:ring-4 focus:ring-indigo-300 rounded-lg shadow-md dark:focus:ring-indigo-800 transition-all duration-300 ease-in-out">
                            {{ __('Daftar Sekarang') }}
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center text-xs text-gray-500 dark:text-gray-400">
            <p>Biro Khairat Kematian Masjid Taman Sutera © {{ date('Y') }}. Hak Cipta Terpelihara.</p>
        </div>
    </div>
</div>

@push('scripts')
{{-- Add any specific JS needed for this view --}}
@endpush
