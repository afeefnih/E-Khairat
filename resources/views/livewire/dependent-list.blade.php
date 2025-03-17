<div>
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg md:text-xl font-semibold text-gray-900 dark:text-white transition-colors duration-200">
            Senarai Tanggungan
        </h2>

        <!-- Add Dependent Button -->
        <button wire:click="addNewDependent"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Tambah Tanggungan
        </button>
    </div>

    <!-- Table for Dependent List -->
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
                @forelse ($dependents as $dependent)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                        {{ $dependent['full_name'] }}
                    </td>
                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                        {{ $dependent['relationship'] }}
                    </td>
                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                        {{ $dependent['age'] }}
                    </td>
                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                        {{ $dependent['ic_number'] }}
                    </td>
                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end gap-2">
                            @if(Auth::check())
                                <button wire:click="editDependent({{ $dependent->dependent_id }})"
                                    class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-500 dark:hover:text-yellow-400 transition-colors duration-200">
                                    Edit
                                </button>
                                <button wire:click="setDependentToDelete({{ $dependent->dependent_id }})"
                                    class="text-red-600 hover:text-red-900 dark:text-red-500 dark:hover:text-red-400">
                                    Delete
                                </button>
                            @else
                                <button wire:click="editDependent({{ $loop->index }})"
                                    class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-500 dark:hover:text-yellow-400 transition-colors duration-200">
                                    Edit
                                </button>
                                <button wire:click="setDependentToDelete({{ $loop->index }})"
                                    class="text-red-600 hover:text-red-900 dark:text-red-500 dark:hover:text-red-400">
                                    Delete
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5"
                        class="px-4 sm:px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        Tiada tanggungan yang didaftarkan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pending Dependents Section (if any) -->
    @if(!empty($pendingDependents))
        <div class="mt-6">
            <div class="flex items-center mb-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Belum Disimpan
                </span>
            </div>

            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 shadow-lg rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-blue-200 dark:divide-blue-800">
                    <thead class="bg-blue-100 dark:bg-blue-800/50">
                        <tr>
                            <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-blue-700 dark:text-blue-300 uppercase tracking-wider">
                                Nama
                            </th>
                            <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-blue-700 dark:text-blue-300 uppercase tracking-wider">
                                Hubungan
                            </th>
                            <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-blue-700 dark:text-blue-300 uppercase tracking-wider">
                                Umur
                            </th>
                            <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-blue-700 dark:text-blue-300 uppercase tracking-wider">
                                Nombor KP
                            </th>
                            <th scope="col" class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-blue-700 dark:text-blue-300 uppercase tracking-wider">
                                Tindakan
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-blue-50 dark:bg-blue-900/10 divide-y divide-blue-200 dark:divide-blue-800">
                        @foreach($pendingDependents as $index => $pending)
                        <tr class="hover:bg-blue-100 dark:hover:bg-blue-800/20 transition-colors duration-200">
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-blue-800 dark:text-blue-200">
                                {{ $pending['full_name'] }}
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-blue-800 dark:text-blue-200">
                                {{ $pending['relationship'] }}
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-blue-800 dark:text-blue-200">
                                {{ $pending['age'] }}
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-blue-800 dark:text-blue-200">
                                {{ $pending['ic_number'] }}
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <button wire:click="editPendingDependent({{ $index }})"
                                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 transition-colors duration-200">
                                        Edit
                                    </button>
                                    <button wire:click="removePendingDependent({{ $index }})"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="mt-6">
        @if(Auth::check())
            <div class="flex justify-end">
                @if(!empty($pendingDependents))
                    <x-button wire:click="savePendingDependents" type="submit" class="bg-blue-600 hover:bg-blue-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan
                    </x-button>
                @endif
            </div>
        @else
            <div class="flex justify-end">
                <x-button wire:click="saveDependents" type="submit">
                    Simpan dan Teruskan
                </x-button>
            </div>
        @endif
    </div>

    <!-- Edit Modal -->
    <div x-data="{ open: @entangle('isModalOpen') }"
        x-show="open"
        @keydown.escape.window="open = false"
        class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">

        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-90 transition-opacity"></div>

        <!-- Modal -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="open"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-xl shadow-2xl transform transition-all">

                <!-- Edit Icon -->
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900 mt-6">
                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>

                <!-- Form Content -->
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white text-center mb-6">
                        @if($editDependentId !== null)
                            Edit Tanggungan
                        @elseif($editPendingIndex !== null)
                            Edit Tanggungan (Belum Disimpan)
                        @else
                            Tambah Tanggungan Baru
                        @endif
                    </h3>

                    <form wire:submit.prevent="{{ $editDependentId !== null ? 'submitEdit' : ($editPendingIndex !== null ? 'submitPending' : 'submitPending') }}" class="space-y-5">
                        <!-- Dependent Full Name -->
                        <div class="space-y-2">
                            <label for="dependent_full_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Nama Penuh
                            </label>
                            <input type="text"
                                id="dependent_full_name"
                                wire:model="dependent_full_name"
                                class="w-full px-4 py-2.5 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-blue-500 dark:focus:border-blue-600 transition-colors duration-200">
                            @error('dependent_full_name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Relationship -->
                        <div class="space-y-2">
                            <label for="dependent_relationship" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Hubungan
                            </label>
                            <select id="dependent_relationship"
                                wire:model="dependent_relationship"
                                class="w-full px-4 py-2.5 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-blue-500 dark:focus:border-blue-600 transition-colors duration-200">
                                <option value="" disabled>Pilih Hubungan</option>
                                <option value="Bapa">Bapa</option>
                                <option value="Ibu">Ibu</option>
                                <option value="Pasangan">Pasangan</option>
                                <option value="Anak">Anak</option>
                            </select>
                            @error('dependent_relationship')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- IC Number -->
                        <div class="space-y-2">
                            <label for="dependent_ic_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Nombor KP
                            </label>
                            <input type="text"
                                id="dependent_ic_number"
                                wire:model="dependent_ic_number"
                                class="w-full px-4 py-2.5 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-blue-500 dark:focus:border-blue-600 transition-colors duration-200">
                            @error('dependent_ic_number')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Age -->
                        <div class="space-y-2">
                            <label for="dependent_age" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Umur
                            </label>
                            <input type="number"
                                id="dependent_age"
                                wire:model="dependent_age"
                                class="w-full px-4 py-2.5 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-blue-500 dark:focus:border-blue-600 transition-colors duration-200">
                            @error('dependent_age')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status Indicator for Pending Items -->
                        @if($editPendingIndex !== null)
                        <div class="px-4 py-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                    Tanggungan ini belum disimpan dalam pangkalan data.
                                </p>
                            </div>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row-reverse gap-3 sm:gap-4 mt-8">
                            <button type="submit"
                                class="w-full sm:w-auto px-4 py-2.5 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white font-medium rounded-lg transition-colors duration-200 flex-1 sm:flex-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                Simpan
                            </button>
                            <button type="button"
                                @click="open = false"
                                class="w-full sm:w-auto px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors duration-200 flex-1 sm:flex-none focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div x-data="{ open: @entangle('isDeleteModalOpen') }" x-show="open" @keydown.escape.window="open = false"
        class="fixed inset-0 z-50 overflow-y-auto" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">

        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-90 transition-opacity">
        </div>

        <!-- Modal -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="open" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="relative w-full max-w-md p-6 bg-white dark:bg-gray-800 rounded-xl shadow-2xl transform transition-all">

                <!-- Warning Icon -->
                <div
                    class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 mb-4">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-300" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                <!-- Title -->
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white text-center mb-4">
                    Pengesahan Padam
                </h3>

                <!-- Message -->
                <p class="mb-6 text-gray-600 dark:text-gray-300 text-center">
                    Adakah anda pasti ingin memadam tanggungan ini? Tindakan ini tidak dapat dipulihkan.
                </p>

                <!-- Status Indicator for Pending Items -->
                @if($isPendingDelete)
                <div class="px-4 py-3 mb-6 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            Item ini belum disimpan dalam pangkalan data.
                        </p>
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row-reverse gap-3 sm:gap-4">
                    <button wire:click="deleteDependent"
                        class="w-full sm:w-auto px-4 py-2.5 bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-white font-medium rounded-lg transition-colors duration-200 flex-1 sm:flex-none focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                        Padam
                    </button>
                    <button @click="open = false"
                        class="w-full sm:w-auto px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors duration-200 flex-1 sm:flex-none focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
