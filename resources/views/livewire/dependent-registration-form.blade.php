<div>
    <h1 class="text-2xl md:text-3xl font-bold mb-6 text-gray-900 dark:text-white transition-colors duration-200">
        Kemaskini Tangungan
    </h1>

    <!-- Add New Dependent Section -->
    <form wire:submit.prevent="submit" class="space-y-6 bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg transition-colors duration-200">
        @csrf

        <h2 class="text-lg md:text-xl font-semibold mb-4 text-gray-900 dark:text-white transition-colors duration-200">
            Tambah Tangungan Baru
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <!-- Dependent Name -->
            <div class="space-y-2">
                <label for="dependent_full_name" class="block font-medium text-gray-700 dark:text-gray-200 transition-colors duration-200">
                    Full Name
                </label>
                <input
                    type="text"
                    name="dependent_full_name"
                    id="dependent_full_name"
                    wire:model="dependent_full_name"
                    class="w-full px-3 py-2 border text-gray-900 bg-white dark:bg-gray-700 dark:text-white border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200"
                    placeholder="Masukkan nama tangungan"
                >
                @error('dependent_full_name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Relationship -->
            <div class="space-y-2">
                <label for="dependent_relationship" class="block font-medium text-gray-700 dark:text-gray-200 transition-colors duration-200">
                    Hubungan
                </label>
                <select
                name="dependent_relationship"
                id="dependent_relationship"
                wire:model="dependent_relationship"
                class="w-full px-3 py-2 text-gray-900 bg-white dark:bg-gray-700 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200"
            >
                <option value="" disabled selected>Pilih Hubungan</option>  <!-- Default option -->
                <option value="Bapa">Bapa</option>
                <option value="Ibu">Ibu</option>
                <option value="Pasangan">Pasangan</option>
                <option value="Anak">Anak</option>
            </select>

                @error('dependent_relationship')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- IC Number -->
            <div class="space-y-2">
                <label for="dependent_ic_number" class="block font-medium text-gray-700 dark:text-gray-200 transition-colors duration-200">
                    No Kad Pengenalan
                </label>
                <input
                    type="text"
                    name="dependent_ic_number"
                    id="dependent_ic_number"
                    wire:model="dependent_ic_number"
                    class="w-full px-3 py-2 text-gray-900 bg-white dark:bg-gray-700 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200"
                    placeholder="Masukkan nombor KP"
                >
                @error('dependent_ic_number')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Age -->
            <div class="space-y-2">
                <label for="dependent_age" class="block font-medium text-gray-700 dark:text-gray-200 transition-colors duration-200">
                    Umur
                </label>
                <input
                    type="number"
                    name="dependent_age"
                    id="dependent_age"
                    wire:model="dependent_age"
                    class="w-full px-3 py-2 text-gray-900 bg-white dark:bg-gray-700 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200"
                    placeholder="Masukkan umur"
                >
                @error('dependent_age')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex flex-col sm:flex-row justify-end gap-4 mt-6">
            <button type="reset" wire:click="resetForm" class="px-4 py-2 bg-gray-300 text-gray-800 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm hover:bg-gray-400 dark:hover:bg-gray-600 transition-colors duration-200">
                Reset
            </button>

            <x-button class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                {{ __('Tambah Tanggungan') }}
            </x-button>

        </div>
    </form>
</div>
