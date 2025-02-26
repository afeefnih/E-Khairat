<x-authentication-card>
    <x-slot name="logo">
        <x-authentication-card-logo />
    </x-slot>

    <x-validation-errors class="mb-4" />

    {{-- Step 1 --}}
    <form wire:submit.prevent="submitForm">
        @csrf

        @if ($currentStep == 1)
            <livewire:registration.step1 />
        @endif

        {{-- Step 2 --}}
        @if ($currentStep == 2)
            <livewire:registration.step2 />
        @endif

        <div class="action-buttons flex justify-between items-center bg-white dark:bg-gray-800 pt-4 pb-4 px-6 rounded-md shadow-md">

            <!-- Step 1: No buttons for Next -->
            @if ($currentStep == 1)
            <div><x-button class="ms-4" wire:click="step1Listener">
                {{ __('Next') }}
            </x-button></div>
            @endif

            <!-- Back Button for Steps 2, 3, and 4 -->
            @if ($currentStep == 2 || $currentStep == 3 )

                <x-button class="btn-back" wire:click="decreaseStep()">
                    {{ __('Back') }}
                </x-button>


            @endif

            <!-- Next Button for Steps 1, 2, and 3 -->
            @if ($currentStep == 1 || $currentStep == 2 || $currentStep == 3)

            <div><x-button class="ms-4" wire:click="$dispatch('submitStep1')">
                {{ __('Next') }}
            </x-button></div>



            @endif

            <!-- Submit Button for Step 4 -->
            @if ($currentStep == 4)
                <button type="submit" class="btn-submit text-white bg-blue-600 hover:bg-blue-500 focus:ring-2 focus:ring-offset-2 focus:ring-blue-700 rounded-lg px-6 py-2.5 transition duration-300">Submit</button>
            @endif

        </div>
    </form>

</x-authentication-card>
