<div>
    <h2 class="text-lg md:text-xl font-semibold mt-10 mb-4 text-gray-900 dark:text-white transition-colors duration-200">
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
                                <button wire:click="editDependent({{ $index }})" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-500 dark:hover:text-yellow-400 transition-colors duration-200">
                                    Edit
                                </button>
                                <button wire:click="deleteDependent({{ $index }})" class="text-red-600 hover:text-red-900 dark:text-red-500 dark:hover:text-red-400 transition-colors duration-200">
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
    <form wire:submit.prevent="submit" class="mt-6">
        @csrf
        <div class="flex justify-end">
            <x-button class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">

                {{ __('Simpan dan Teruskan') }}
            </x-button>
        </div>
    </form>
</div>
