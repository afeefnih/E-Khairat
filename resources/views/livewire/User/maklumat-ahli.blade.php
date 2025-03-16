

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <section class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="py-8 px-4 mx-auto max-w-4xl lg:py-16">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Edit Your Profile</h2>
                        <!-- User ID Badge -->
                        <div class="bg-gray-100 dark:bg-gray-700 px-3 py-1.5 rounded-lg flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9a3 3 0 100-6 3 3 0 000 6z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19v-4a5 5 0 0110 0v4" />
                            </svg>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Member ID: {{ Auth::user()->No_Ahli }}</span>
                        </div>
                    </div>



                    <form wire:submit.prevent="updateProfileInformation">
                        <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
                            <!-- Name -->
                            <div class="sm:col-span-2">
                                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Full Name</label>
                                <input type="text" name="name" id="name" wire:model="state.name" placeholder="Your full name"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
                                    >
                                <x-input-error for="name" class="mt-1" />
                            </div>


                            <!-- Email -->
                            <div class="sm:col-span-2">
                                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email Address</label>
                                <input type="email" name="email" id="email" wire:model="state.email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500" placeholder="your.email@example.com" >
                                <x-input-error for="email" class="mt-1" />

                                @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! Auth::user()->hasVerifiedEmail())
                                    <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-lg flex items-start dark:bg-yellow-900/20 dark:border-yellow-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400 mr-2 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        <div>
                                            <p class="text-sm text-yellow-700 dark:text-yellow-200">
                                                {{ __('Your email address is unverified.') }}
                                                <button type="button" class="font-medium underline text-yellow-700 dark:text-yellow-200 hover:text-yellow-800 dark:hover:text-yellow-100" wire:click.prevent="sendEmailVerification">
                                                    {{ __('Click here to re-send the verification email.') }}
                                                </button>
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- IC Number -->
                            <div>
                                <label for="ic_number" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">IC Number</label>
                                <input type="text" name="ic_number" id="ic_number" wire:model="state.ic_number" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500" placeholder="Enter your IC number" >
                                <x-input-error for="ic_number" class="mt-1" />
                            </div>

                            <!-- Age -->
                            <div>
                                <label for="age" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Age</label>
                                <input type="number" name="age" id="age" wire:model="state.age" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500" placeholder="Your age" >
                                <x-input-error for="age" class="mt-1" />
                            </div>

                            <!-- Phone Number -->
                            <div>
                                <label for="phone_number" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Phone Number</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </div>
                                    <input type="text" name="phone_number" id="phone_number" wire:model="state.phone_number" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500" placeholder="Your mobile phone number" >
                                </div>
                                <x-input-error for="phone_number" class="mt-1" />
                            </div>

                            <!-- Home Phone -->
                            <div>
                                <label for="home_phone" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Home Phone</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                        </svg>
                                    </div>
                                    <input type="text" name="home_phone" id="home_phone" wire:model="state.home_phone" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500" placeholder="Your home phone number" >
                                </div>
                                <x-input-error for="home_phone" class="mt-1" />
                            </div>

                            <!-- Residence Status -->
                            <div>
                                <label for="residence_status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Residence Status</label>
                                <select id="residence_status" wire:model="state.residence_status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500">
                                    <option value="" disabled selected>Select your residence status</option>
                                    <option value="kekal">Kekal</option>
                                    <option value="sewa">Sewa</option>
                                </select>
                                <x-input-error for="residence_status" class="mt-1" />
                            </div>

                            <!-- Address -->
                            <div class="sm:col-span-2">
                                <label for="address" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Address</label>
                                <textarea id="address" rows="3" wire:model="state.address" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500" placeholder="Your full address"></textarea>
                                <x-input-error for="address" class="mt-1" />
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end space-x-4 mt-6">
                            <button type="button" class="text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-indigo-700">
                                Cancel
                            </button>
                            <button type="submit"  class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-indigo-600 rounded-lg focus:ring-4 focus:ring-indigo-200 dark:focus:ring-indigo-900 hover:bg-indigo-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>

