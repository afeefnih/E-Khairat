<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-indigo-950 py-8 mt-10">
    <div class="container max-w-screen-xl mx-auto px-4">
        <!-- Page Header -->
        <div class="text-center mb-8">
            <div class="mx-auto h-24 w-24 mb-4">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-contain">
            </div>
            
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                Pendaftaran Ahli Khairat
            </h1>
            <p class="mt-2 text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                Sila lengkapkan maklumat anda untuk mendaftar
            </p>
        </div>

        <!-- Progress Bar -->
        <div class="max-w-3xl mx-auto mb-8 px-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-indigo-700 dark:text-indigo-300">Langkah 1 dari 3</span>
                <span class="text-sm font-medium text-indigo-700 dark:text-indigo-300">Pendaftaran Pengguna</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                <div class="bg-indigo-600 dark:bg-indigo-500 h-2.5 rounded-full" style="width: 33%"></div>
            </div>
        </div>

        <!-- Card Container -->
        <div class="max-w-4xl mx-auto">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden">
                <!-- Header Section with Logo -->
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 p-6 md:p-8">
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-20 h-20 bg-white dark:bg-gray-700 rounded-full p-1 shadow-lg mb-4 flex items-center justify-center">
                            <svg class="w-12 h-12 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl md:text-2xl font-bold text-white text-center">
                            Maklumat Pengguna
                        </h2>
                    </div>
                </div>

                <!-- Form Content -->
                <div class="p-6 md:p-8">
                    <x-validation-errors class="mb-6 rounded-lg bg-red-50 dark:bg-red-900/20 p-4 text-red-600 dark:text-red-400" />

                    <form wire:submit.prevent="submit" class="space-y-6">
                        @csrf

                        <!-- Personal Information Section -->
                        <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4 mb-6">
                            <h2 class="text-lg font-semibold text-indigo-800 dark:text-indigo-300 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Maklumat Peribadi
                            </h2>
                            
                            <!-- Full Name (Nama Penuh) -->
                            <div class="relative z-0 w-full mb-5 group">
                                <input type="text" wire:model="name" id="name"
                                       class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-indigo-500 focus:outline-none focus:ring-0 focus:border-indigo-600 peer"
                                       placeholder=" " autofocus autocomplete="name" />
                                <label for="name"
                                       class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-indigo-600 peer-focus:dark:text-indigo-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                    {{ __('Nama Penuh') }} <span class="text-red-500">*</span>
                                </label>
                            </div>

                            <!-- Two Column Layout -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
                                <!-- Kad Pengenalan (IC Number) -->
                                <div class="relative z-0 w-full mb-5 group">
                                    <input type="text" wire:model="ic_number" id="ic_number"
                                           class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-indigo-500 focus:outline-none focus:ring-0 focus:border-indigo-600 peer"
                                           placeholder=" " />
                                    <label for="ic_number"
                                           class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-indigo-600 peer-focus:dark:text-indigo-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                        {{ __('Kad Pengenalan') }} <span class="text-red-500">*</span>
                                    </label>
                                </div>

                                <!-- Age (Umur) -->
                                <div class="relative z-0 w-full mb-5 group">
                                    <input type="number" wire:model="age" id="age"
                                           class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-indigo-500 focus:outline-none focus:ring-0 focus:border-indigo-600 peer"
                                           placeholder=" " />
                                    <label for="age"
                                           class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-indigo-600 peer-focus:dark:text-indigo-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                        {{ __('Umur') }} <span class="text-red-500">*</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information Section -->
                        <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4 mb-6">
                            <h2 class="text-lg font-semibold text-indigo-800 dark:text-indigo-300 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                Maklumat Perhubungan
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
                                <!-- Phone Number (No Telefon) -->
                                <div class="relative z-0 w-full mb-5 group">
                                    <input type="text" wire:model="phone_number" id="phone_number"
                                           class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-indigo-500 focus:outline-none focus:ring-0 focus:border-indigo-600 peer"
                                           placeholder=" " />
                                    <label for="phone_number"
                                           class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-indigo-600 peer-focus:dark:text-indigo-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                        {{ __('No Telefon') }} <span class="text-red-500">*</span>
                                    </label>
                                </div>

                                <!-- Home Phone (No Telefon Rumah) -->
                                <div class="relative z-0 w-full mb-5 group">
                                    <input type="text" wire:model="home_phone" id="home_phone"
                                           class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-indigo-500 focus:outline-none focus:ring-0 focus:border-indigo-600 peer"
                                           placeholder=" " />
                                    <label for="home_phone"
                                           class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-indigo-600 peer-focus:dark:text-indigo-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                        {{ __('No Telefon Rumah') }}
                                    </label>
                                </div>
                            </div>

                            <!-- Address (Alamat) -->
                            <div class="relative z-0 w-full mb-5 group">
                                <input type="text" wire:model="address" id="address"
                                       class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-indigo-500 focus:outline-none focus:ring-0 focus:border-indigo-600 peer"
                                       placeholder=" " />
                                <label for="address"
                                       class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-indigo-600 peer-focus:dark:text-indigo-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                    {{ __('Alamat') }} <span class="text-red-500">*</span>
                                </label>
                            </div>

                            <!-- Residence Status (Status Permastautin) -->
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="residence_status" class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 block">
                                    {{ __('Status Permastautin') }} <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select name="residence_status" id="residence_status" wire:model="residence_status"
                                        class="appearance-none block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
                                    >
                                        <option value="" disabled selected>Pilih Status Permastautin</option>
                                        <option value="kekal">Kekal</option>
                                        <option value="sewa">Sewa</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-300">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Email (optional) -->
                            <div class="relative z-0 w-full mb-5 group">
                                <input type="email" wire:model="email" name="email" id="email"
                                       class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-indigo-500 focus:outline-none focus:ring-0 focus:border-indigo-600 peer"
                                       placeholder=" " />
                                <label for="email"
                                       class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-indigo-600 peer-focus:dark:text-indigo-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                    {{ __('Email (Optional)') }}
                                </label>
                            </div>
                        </div>

                        <!-- Account Setup Section -->
                        <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4 mb-6">
                            <h2 class="text-lg font-semibold text-indigo-800 dark:text-indigo-300 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                Maklumat Akaun
                            </h2>
                            
                            <!-- Password -->
                            <div class="relative z-0 w-full mb-5 group">
                                <input type="password" wire:model="password" name="password" id="password"
                                       class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-indigo-500 focus:outline-none focus:ring-0 focus:border-indigo-600 peer"
                                       placeholder=" " autocomplete="new-password" />
                                <label for="password"
                                       class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-indigo-600 peer-focus:dark:text-indigo-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                    {{ __('Kata Laluan') }} <span class="text-red-500">*</span>
                                </label>
                            </div>
                            
                            <!-- Confirm Password -->
                            <div class="relative z-0 w-full mb-5 group">
                                <input type="password" wire:model="password_confirmation" name="password_confirmation" id="password_confirmation"
                                       class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-indigo-500 focus:outline-none focus:ring-0 focus:border-indigo-600 peer"
                                       placeholder=" " autocomplete="new-password" />
                                <label for="password_confirmation"
                                       class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-indigo-600 peer-focus:dark:text-indigo-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                    {{ __('Sahkan Kata Laluan') }} <span class="text-red-500">*</span>
                                </label>
                            </div>
                        </div>

                      

                        <!-- Submit Button -->
                        <div class="flex flex-col sm:flex-row items-center justify-between mt-8 gap-4">
                            <a wire:navigate class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 flex items-center" href="{{ route('login') }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                                </svg>
                                {{ __('Sudah Daftar?') }}
                            </a>

                            <button type="submit" class="w-full sm:w-auto px-6 py-3 text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm flex items-center justify-center dark:bg-indigo-700 dark:hover:bg-indigo-800 dark:focus:ring-indigo-800 transition-colors">
                                {{ __('Daftar Sekarang') }}
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Additional information -->
            <div class="mt-4 text-center text-xs text-gray-600 dark:text-gray-400">
                <p>Biro Khairat Kematian Masjid Taman Sutera © 2025. Hak Cipta Terpelihara.</p>
            </div>
        </div>
    </div>
</div>