<x-form-section submit="updateProfileInformation" >
    <x-slot name="title">
        <span class="text-lg font-semibold dark:text-white">{{ __('Profile Information') }}</span>
    </x-slot>

    <x-slot name="description">

        <span class="text-sm dark:text-gray-400">{{ __('Update your account\'s profile information and email address.') }}</span>

    </x-slot>

    <x-slot name="form">

        <!-- Name -->
        <div class="col-span-6 sm:col-span-4 ">
            <x-label for="name" value="{{ __('Name') }}" class="" />
            <x-input id="name" type="text" class="mt-1 block w-full dark:bg-gray-700 dark:text-white dark:border-gray-600" wire:model="state.name" required autocomplete="name" />
            <x-input-error for="name" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="email" value="{{ __('Email') }}" class="dark:text-gray-300" />
            <x-input id="email" type="email" class="mt-1 block w-full dark:bg-gray-700 dark:text-white dark:border-gray-600" wire:model="state.email" required autocomplete="username" />
            <x-input-error for="email" class="mt-2" />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                <p class="text-sm mt-2 dark:text-gray-400">
                    {{ __('Your email address is unverified.') }}

                    <button type="button" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" wire:click.prevent="sendEmailVerification">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>

                @if ($this->verificationLinkSent)
                    <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-300">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                @endif
            @endif
        </div>

        <!-- ic_number -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="ic_number" value="{{ __('IC Number') }}" class="dark:text-gray-300" />
            <x-input id="ic_number" type="text" class="mt-1 block w-full dark:bg-gray-700 dark:text-white dark:border-gray-600" wire:model="state.ic_number" required autocomplete="ic_number" />
            <x-input-error for="ic_number" class="mt-2" />
        </div>

        <!-- phone_number -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="phone_number" value="{{ __('Phone Number') }}" class="dark:text-gray-300" />
            <x-input id="phone_number" type="text" class="mt-1 block
            w-full dark:bg-gray-700 dark:text-white dark:border-gray-600" wire:model="state.phone_number" required autocomplete="phone_number" />
            <x-input-error for="phone_number" class="mt-2" />
        </div>

        <!-- address -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="address" value="{{ __('Address') }}" class="dark:text-gray-300" />
            <x-input id="address" type="text" class="mt-1 block w-full dark:bg-gray-700 dark:text-white dark:border-gray-600" wire:model="state.address" required autocomplete="address" />
            <x-input-error for="address" class="mt-2" />
        </div>

        <!-- home phone -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="home_phone" value="{{ __('Home Phone') }}" class="dark:text-gray-300" />
            <x-input id="home_phone" type="text" class="mt-1 block w-full dark:bg-gray-700 dark:text-white dark:border-gray-600" wire:model="state.home_phone" required autocomplete="home_phone" />
            <x-input-error for="home_phone" class="mt-2" />
        </div>

        <!--resident status -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="residence_status" value="{{ __('Resident Status') }}" class="dark:text-gray-300" />

            <select id="residence_status" name="residence_status"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300"
                    wire:model="state.residence_status" required>
                <option value="" disabled selected>Select status</option>
                <option value="kekal">Kekal</option>
                <option value="sewa">Sewa</option>
            </select>

            <x-input-error for="residence_status" class="mt-2" />
        </div>



    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Saved.') }}
        </x-action-message>

        <x-button wire:loading.attr="disabled" wire:target="photo" class="dark:bg-blue-700 dark:hover:bg-blue-800 dark:focus:ring-blue-500">
            {{ __('Save') }}
        </x-button>
    </x-slot>
</x-form-section>
