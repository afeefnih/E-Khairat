<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-label for="name" value="{{ __('Name') }}" class="dark:text-gray-400" />
                <x-input id="name" class="block mt-1 w-full dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:focus:ring-gray-500" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('Email') }}" class="dark:text-gray-400" />
                <x-input id="email" class="block mt-1 w-full dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:focus:ring-gray-500" type="email" name="email" :value="old('email')" required autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" class="dark:text-gray-400" />
                <x-input id="password" class="block mt-1 w-full dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:focus:ring-gray-500" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" class="dark:text-gray-400" />
                <x-input id="password_confirmation" class="block mt-1 w-full dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:focus:ring-gray-500" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms" class="dark:text-gray-400">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required class="dark:bg-gray-700 dark:border-gray-500 dark:checked:bg-indigo-600" />
                            <div class="ms-2 dark:text-white">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                    'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:text-gray-400 dark:hover:text-white">'.__('Terms of Service').'</a>',
                                    'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:text-gray-400 dark:hover:text-white">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a wire:navigate class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:text-gray-400 dark:hover:text-white" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-button class="ms-4 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
