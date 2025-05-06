<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <section class="bg-white dark:bg-gray-900 rounded-lg shadow-md overflow-hidden">
            <!-- Header with decorative gradient -->
            <div class="bg-gradient-to-r from-indigo-500 to-indigo-700 dark:from-indigo-800 dark:to-indigo-950 py-4 px-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Maklumat Peribadi
                    </h2>
                    <!-- User ID Badge -->
                    <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-lg flex items-center shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9a3 3 0 100-6 3 3 0 000 6z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19v-4a5 5 0 0110 0v4" />
                        </svg>
                        <span class="text-sm font-medium text-white">ID Ahli: {{ Auth::user()->No_Ahli }}</span>
                    </div>
                </div>
            </div>

            <div class="py-8 px-4 mx-auto max-w-4xl lg:py-10">
                <!-- Status Bar -->
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex items-center">

                    </div>
                    <!-- Edit Toggle Button -->
                    <button
                        type="button"
                        wire:click="toggleEditMode"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-center rounded-lg focus:ring-4 focus:outline-none transition-colors duration-200
                        {{ $editMode
                            ? 'text-white bg-red-600 hover:bg-red-700 focus:ring-red-300 dark:focus:ring-red-800'
                            : 'text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-300 dark:focus:ring-indigo-800'
                        }}"
                    >
                        @if($editMode)
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Batal Kemaskini</span>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span>Kemaskini Profil</span>
                        @endif
                    </button>
                </div>

                <form wire:submit.prevent="updateProfileInformation">
                    <!-- Form Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <!-- Personal Info Section -->
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-500 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Maklumat Asas
                            </h3>
                            <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
                                <!-- Name -->
                                <div class="sm:col-span-2">
                                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Penuh</label>
                                    <input
                                        type="text"
                                        name="name"
                                        id="name"
                                        wire:model="state.name"
                                        placeholder="Nama penuh anda"
                                        class="border text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:text-white
                                        {{ $editMode
                                            ? 'bg-gray-50 border-gray-300 focus:ring-indigo-600 focus:border-indigo-600 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:focus:ring-indigo-500 dark:focus:border-indigo-500'
                                            : 'bg-gray-100 border-gray-200 cursor-not-allowed dark:bg-gray-800 dark:border-gray-700'
                                        }}"
                                        {{ $editMode ? '' : 'readonly' }}
                                    >
                                    @error('state.name') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>

                                <!-- IC Number -->
                                <div>
                                    <label for="ic_number" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No. Kad Pengenalan</label>
                                    <input
                                        type="text"
                                        name="ic_number"
                                        id="ic_number"
                                        wire:model="state.ic_number"
                                        maxlength="12" minlength="12" pattern="\d{12}"
                                        inputmode="numeric"
                                        class="border text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:text-white
                                        {{ $editMode
                                            ? 'bg-gray-50 border-gray-300 focus:ring-indigo-600 focus:border-indigo-600 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:focus:ring-indigo-500 dark:focus:border-indigo-500'
                                            : 'bg-gray-100 border-gray-200 cursor-not-allowed dark:bg-gray-800 dark:border-gray-700'
                                        }}"
                                        placeholder="xxxxxxxxxxxx"
                                        {{ $editMode ? '' : 'readonly' }}
                                    >
                                    @error('state.ic_number') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>

                                <!-- Age -->
                                <div>
                                    <label for="age" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Umur</label>
                                    <input
                                        type="number"
                                        name="age"
                                        id="age"
                                        wire:model="state.age"
                                        readonly
                                        class="border text-gray-500 text-sm rounded-lg block w-full p-2.5 bg-gray-50 dark:text-gray-400 dark:bg-gray-700 dark:border-gray-600 cursor-not-allowed"
                                        placeholder="Umur anda"
                                    >
                                    @error('state.age') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Contact Info Section -->
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-500 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                Maklumat Perhubungan
                            </h3>
                            <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
                                <!-- Email -->
                                <div class="sm:col-span-2">
                                    <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat Email</label>
                                    <input
                                        type="email"
                                        name="email"
                                        id="email"
                                        wire:model="state.email"
                                        class="border text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:text-white
                                        {{ $editMode
                                            ? 'bg-gray-50 border-gray-300 focus:ring-indigo-600 focus:border-indigo-600 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:focus:ring-indigo-500 dark:focus:border-indigo-500'
                                            : 'bg-gray-100 border-gray-200 cursor-not-allowed dark:bg-gray-800 dark:border-gray-700'
                                        }}"
                                        placeholder="email.anda@contoh.com"
                                        {{ $editMode ? '' : 'readonly' }}
                                    >
                                    @error('state.email') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror

                                    @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! Auth::user()->hasVerifiedEmail())
                                        <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-lg flex items-start dark:bg-yellow-900/20 dark:border-yellow-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400 mr-2 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                            <div>
                                                <p class="text-sm text-yellow-700 dark:text-yellow-200">
                                                    {{ __('Alamat email anda belum disahkan.') }}
                                                    <button type="button" class="font-medium underline text-yellow-700 dark:text-yellow-200 hover:text-yellow-800 dark:hover:text-yellow-100" wire:click.prevent="sendEmailVerification">
                                                        {{ __('Klik di sini untuk menghantar semula email pengesahan.') }}
                                                    </button>
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Phone Number -->
                                <div>
                                    <label for="phone_number" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No. Telefon Bimbit</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                        </div>
                                        <input
                                            type="text"
                                            name="phone_number"
                                            id="phone_number"
                                            wire:model="state.phone_number"
                                            class="border text-gray-900 text-sm rounded-lg block w-full pl-10 p-2.5 dark:text-white
                                            {{ $editMode
                                                ? 'bg-gray-50 border-gray-300 focus:ring-indigo-600 focus:border-indigo-600 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:focus:ring-indigo-500 dark:focus:border-indigo-500'
                                                : 'bg-gray-100 border-gray-200 cursor-not-allowed dark:bg-gray-800 dark:border-gray-700'
                                            }}"
                                            placeholder="No. telefon bimbit anda"
                                            {{ $editMode ? '' : 'readonly' }}
                                        >
                                    </div>
                                    @error('state.phone_number') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>

                                <!-- Home Phone -->
                                <div>
                                    <label for="home_phone" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No. Telefon Rumah</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                            </svg>
                                        </div>
                                        <input
                                            type="text"
                                            name="home_phone"
                                            id="home_phone"
                                            wire:model="state.home_phone"
                                            class="border text-gray-900 text-sm rounded-lg block w-full pl-10 p-2.5 dark:text-white
                                            {{ $editMode
                                                ? 'bg-gray-50 border-gray-300 focus:ring-indigo-600 focus:border-indigo-600 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:focus:ring-indigo-500 dark:focus:border-indigo-500'
                                                : 'bg-gray-100 border-gray-200 cursor-not-allowed dark:bg-gray-800 dark:border-gray-700'
                                            }}"
                                            placeholder="No. telefon rumah anda"
                                            {{ $editMode ? '' : 'readonly' }}
                                        >
                                    </div>
                                    @error('state.home_phone') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Address Section -->
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-500 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Maklumat Alamat
                            </h3>
                            <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
                                <!-- Residence Status -->
                                <div>
                                    <label for="residence_status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status Kediaman</label>
                                    <select
                                        id="residence_status"
                                        wire:model="state.residence_status"
                                        class="border text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:text-white
                                        {{ $editMode
                                            ? 'bg-gray-50 border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:focus:ring-indigo-500 dark:focus:border-indigo-500'
                                            : 'bg-gray-100 border-gray-200 cursor-not-allowed dark:bg-gray-800 dark:border-gray-700'
                                        }}"
                                        {{ $editMode ? '' : 'disabled' }}
                                    >
                                        <option value="" disabled selected>Pilih status kediaman anda</option>
                                        <option value="kekal">Kekal</option>
                                        <option value="sewa">Sewa</option>
                                    </select>
                                    @error('state.residence_status') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>

                                <!-- Address -->
                                <div class="sm:col-span-2">
                                    <label for="address" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat Penuh</label>
                                    <textarea
                                        id="address"
                                        rows="3"
                                        wire:model="state.address"
                                        class="border text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:text-white
                                        {{ $editMode
                                            ? 'bg-gray-50 border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:focus:ring-indigo-500 dark:focus:border-indigo-500'
                                            : 'bg-gray-100 border-gray-200 cursor-not-allowed dark:bg-gray-800 dark:border-gray-700'
                                        }}"
                                        placeholder="Alamat penuh anda"
                                        {{ $editMode ? '' : 'readonly' }}
                                    ></textarea>
                                    @error('state.address') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    @if($editMode)
                    <div class="flex items-center justify-end space-x-4 mt-6">
                        <button type="button" wire:click="cancelEdit" class="text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-indigo-700 transition-colors duration-200">
                            Batal
                        </button>
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-indigo-600 rounded-lg focus:ring-4 focus:ring-indigo-200 dark:focus:ring-indigo-900 hover:bg-indigo-700 transition-colors duration-200" wire:poll.500ms>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            Simpan Perubahan
                        </button>
                    </div>
                    @endif
                </form>
            </div>

            <!-- Dependent Section -->

        </section>

           <!-- Dependent List Section (Enhanced) -->
           <section class="bg-white dark:bg-gray-900 rounded-lg shadow-md mt-6 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-500 to-indigo-700 dark:from-indigo-800 dark:to-indigo-950 py-4 px-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Senarai Tanggungan
                    </h2>


                </div>
            </div>

            <div class="p-6">
                <!-- Dependents Table -->
                <livewire:dependent-list/>

                <!-- Information Card -->
                <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                Tanggungan mestilah terdiri daripada isteri atau suami, anak-anak yang belum berkahwin, ibubapa kandung, atau ibu bapa mertua yang tinggal serumah dengan ahli. Anak yang telah berumah tangga atau ibubapa yang tidak tinggal serumah tidak dikira sebagai tanggungan dan perlu mendaftar secara berasingan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
