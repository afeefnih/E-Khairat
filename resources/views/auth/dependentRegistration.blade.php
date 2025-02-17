<x-authentication-card>
    <x-slot name="logo">
        <x-authentication-card-logo />
    </x-slot>

    <x-validation-errors class="mb-4" />

    <div class="container">
        <!-- Dependent Registration Form -->
        <livewire:dependent-registration-form />

        <!-- List of Dependents -->
        <livewire:dependent-list />
    </div>
</x-authentication-card>
