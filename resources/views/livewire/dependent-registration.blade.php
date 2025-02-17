<x-authentication-card>
    <x-slot name="logo">
        <x-authentication-card-logo />
    </x-slot>

    <x-validation-errors class="mb-4" />


        <h1 class="text-2xl md:text-3xl font-bold mb-6 text-gray-900 dark:text-white transition-colors duration-200">
            Kemaskini Tangungan
        </h1>

        <!-- Add New Dependent Section -->
        <form method="POST" action="#" class="space-y-6 bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg transition-colors duration-200">
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
                        class="w-full px-3 py-2 border text-gray-900 bg-white dark:bg-gray-700 dark:text-white border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors duration-200"
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
                        class="w-full px-3 py-2 text-gray-900 bg-white dark:bg-gray-700 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors duration-200"
                    >
                        <option value="" disabled selected>Pilih Hubungan</option>
                        <option value="Bapa">Bapa</option>
                        <option value="Ibu">Ibu</option>
                        <option value="Adik/Abang/Kakak">Adik/Abang/Kakak</option>
                        <option value="Pasangan">Pasangan</option>
                        <option value="Anak">Anak</option>
                    </select>
                    @error('dependent_relationship')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- IC Number -->
                <div class="space-y-2">
                    <label for="dependent_ic_num" class="block font-medium text-gray-700 dark:text-gray-200 transition-colors duration-200">
                        No Kad Pengenalan
                    </label>
                    <input
                        type="text"
                        name="dependent_ic_num"
                        id="dependent_ic_num"
                        class="w-full px-3 py-2 text-gray-900 bg-white dark:bg-gray-700 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors duration-200"
                        placeholder="Masukkan nombor KP"
                    >
                    @error('dependent_ic_num')
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
                        class="w-full px-3 py-2 text-gray-900 bg-white dark:bg-gray-700 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors duration-200"
                        placeholder="Masukkan umur"
                    >
                    @error('dependent_age')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex flex-col sm:flex-row justify-end gap-4 mt-6">
                <button type="reset" class="px-4 py-2 bg-gray-300 text-gray-800 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm hover:bg-gray-400 dark:hover:bg-gray-600 transition-colors duration-200">
                    Reset
                </button>
                <x-button class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    {{ __('Tambah Tanggungan') }}
                </x-button>
            </div>
        </form>

        <!-- Existing Dependents Section -->
        <h2 id="tambah-tanggungan" class="text-lg md:text-xl font-semibold mt-10 mb-4 text-gray-900 dark:text-white transition-colors duration-200">
            Senarai Tangungan
        </h2>

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-x-auto transition-colors duration-200">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700 transition-colors duration-200">
                    <tr>
                        <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Nama
                        </th>
                        <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Hubungan
                        </th>
                        <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Umur
                        </th>
                        <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Nombor KP
                        </th>
                        <th scope="col" class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Tindakan
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:divide-gray-700 dark:bg-gray-800">
                    @forelse ($dependents as $index => $dependent)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $dependent['dependent_full_name'] }}
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $dependent['dependent_relationship'] }}
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $dependent['dependent_age'] }}
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $dependent['dependent_ic_number'] }}
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <button
                                        wire:click="editDependent({{ $index }})"
                                        class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-500 dark:hover:text-yellow-400 transition-colors duration-200"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        wire:click="deleteDependent({{ $index }})"
                                        class="text-red-600 hover:text-red-900 dark:text-red-500 dark:hover:text-red-400 transition-colors duration-200"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 sm:px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                Tiada tanggungan yang didaftarkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Save Button -->
        <form method="POST" action="#" class="mt-6">
            @csrf
            <div class="flex justify-end">
                <x-button class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">

                    {{ __('Simpan dan Teruskan') }}
                </x-button>
            </div>
        </form>


    <script>
        document.querySelectorAll('form[data-action="delete"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to delete this dependent?')) {
                    e.preventDefault();
                }
            });
        });
    </script>

</x-authentication-card>
