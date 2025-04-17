<x-guest-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-indigo-950 py-8">
        <div class="container max-w-screen-xl mx-auto px-4">
            <!-- Page Header -->
            <div class="text-center mb-8 mt-10">
                <div class="flex justify-center mx-auto mb-4">
                    <a wire:navigate href="/">
                        <img src="{{asset ('images/logo.png')}}" alt="Logo" class="h-24 w-24 object-contain">
                    </a>
                </div>
                
                <h1 class="mt-4 text-3xl md:text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                    Pendaftaran Tanggungan
                </h1>
                <p class="mt-3 text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    Sila daftarkan semua tanggungan yang akan dilindungi di bawah khairat kematian anda.
                </p>
            </div>

            <!-- Main Content -->
            <div class="max-w-4xl mx-auto">
                <!-- Progress Bar -->
                <div class="mb-8 px-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-indigo-700 dark:text-indigo-300">Langkah 2 dari 3</span>
                        <span class="text-sm font-medium text-indigo-700 dark:text-indigo-300">Pendaftaran Tanggungan</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                        <div class="bg-indigo-600 dark:bg-indigo-500 h-2.5 rounded-full" style="width: 66%"></div>
                    </div>
                </div>

                <!-- Cards Container -->
                <div class="grid grid-cols-1 gap-8">
                    <!-- Dependent Registration Form Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 py-4 px-6">
                            <h2 class="text-xl font-bold text-white flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                Tambah Tanggungan Baru
                            </h2>
                        </div>
                        <div class="p-6">
                            <livewire:dependent-registration-form />
                        </div>
                    </div>

                    <!-- List of Dependents Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 py-4 px-6">
                            <h2 class="text-xl font-bold text-white flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Senarai Tanggungan
                            </h2>
                        </div>
                        <div class="p-6">
                            <livewire:dependent-list />
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // This will trigger the save dependents method in the Livewire component
            document.getElementById('continue-button').addEventListener('click', function() {
                Livewire.emit('saveDependentsAndContinue');
            });
        });
    </script>
</x-guest-layout>
