<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div>
                <x-label for="kad_pengenalan" value="{{ __('Kad Pengenalan') }}" class="dark:text-gray-400" />
                <x-input id="kad_pengenalan" class="block mt-1 w-full dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:focus:ring-gray-500" type="text" name="kad_pengenalan" required autofocus />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" class="dark:text-gray-400" />
                <x-input id="password" class="block mt-1 w-full dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:focus:ring-gray-500" type="password" ... />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center dark:text-gray-400">  <x-checkbox id="remember_me" name="remember" class="dark:bg-gray-700 dark:border-gray-500 dark:checked:bg-indigo-600" />  <span class="ms-2 text-sm text-gray-600 dark:text-white">  {{ __('Remember me') }}
                    </span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a wire:navigate class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:text-gray-400 dark:hover:text-white" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-button class="ms-4 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                    {{ __('Log in') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
