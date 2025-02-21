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
            <!-- IC Number -->
            <div class="mb-4">
                <label for="ic_number">Kad Pengenalan</label>
                <input type="text" name="ic_number" required>
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password">Password</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit">Login</button>
        </form>

    </x-authentication-card>
</x-guest-layout>
