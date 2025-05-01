<div>
    <!-- Add New Dependent Section -->
    <form wire:submit.prevent="submit" class="space-y-6">
        @csrf

        <!-- Form Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Dependent Name -->
            <div class="space-y-2">
                <label for="dependent_full_name" class="block font-medium text-gray-700 dark:text-gray-200 transition-colors duration-200">
                    Nama Penuh <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        name="dependent_full_name"
                        id="dependent_full_name"
                        wire:model="dependent_full_name"
                        class="pl-10 w-full px-4 py-2.5 border text-gray-900 bg-white dark:bg-gray-700 dark:text-white border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-200"
                        placeholder="Masukkan nama tanggungan"
                    >
                </div>
                @error('dependent_full_name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Relationship -->
            <div class="space-y-2">
                <label for="dependent_relationship" class="block font-medium text-gray-700 dark:text-gray-200 transition-colors duration-200">
                    Hubungan <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <select
                        name="dependent_relationship"
                        id="dependent_relationship"
                        wire:model="dependent_relationship"
                        class="pl-10 w-full px-4 py-2.5 text-gray-900 bg-white dark:bg-gray-700 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-200 appearance-none"
                    >
                        <option value="" disabled selected>Pilih Hubungan</option>
                        <option value="Bapa">Bapa</option>
                        <option value="Ibu">Ibu</option>
                        <option value="Pasangan">Pasangan</option>
                        <option value="Anak">Anak</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-300">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                        </svg>
                    </div>
                </div>
                @error('dependent_relationship')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- IC Number -->
            <div class="space-y-2">
                <label for="dependent_ic_number" class="block font-medium text-gray-700 dark:text-gray-200 transition-colors duration-200">
                    No Kad Pengenalan <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        name="dependent_ic_number"
                        id="dependent_ic_number"
                        wire:model.live="dependent_ic_number"
                        maxlength="12"
                        pattern="\d{12}"
                        inputmode="numeric"
                        class="pl-10 w-full px-4 py-2.5 text-gray-900 bg-white dark:bg-gray-700 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-200"
                        placeholder="Masukkan nombor KP"
                    >
                </div>
                @error('dependent_ic_number')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Age -->
            <div class="space-y-2">
                <label for="dependent_age" class="block font-medium text-gray-700 dark:text-gray-200 transition-colors duration-200">
                    Umur <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <input
                        type="number"
                        name="dependent_age"
                        id="dependent_age"
                        wire:model="dependent_age"
                        readonly
                        class="pl-10 w-full px-4 py-2.5 text-gray-500 bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-200 cursor-not-allowed"
                        placeholder="Umur akan dijana"
                    >
                </div>
                @error('dependent_age')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Information Box -->
        <div class="bg-indigo-50 dark:bg-indigo-900/20 border-l-4 border-indigo-500 p-4 rounded-r-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-indigo-700 dark:text-indigo-300">
                        Sila pastikan maklumat yang dimasukkan adalah tepat. Maklumat ini akan digunakan untuk tujuan pengesahan identiti. Umur akan dikira secara automatik berdasarkan Nombor Kad Pengenalan.
                    </p>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex flex-col sm:flex-row justify-end gap-4 mt-6">
            <button type="reset" wire:click="resetForm" class="inline-flex items-center justify-center px-4 py-2 bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200 rounded-lg shadow-sm hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Reset
            </button>

            @if(Auth::check())
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-800 text-white rounded-lg shadow-sm transition-colors duration-200" wire:submit.prevent="addNewDependent">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ __('Kemaskini') }}
                </button>
            @else
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-800 text-white rounded-lg shadow-sm transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    {{ __('Tambah Tanggungan') }}
                </button>
            @endif
        </div>
    </form>
</div>
