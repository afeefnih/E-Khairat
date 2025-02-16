<div>
    <!-- Step Indicators -->
    <div class="flex mb-4">
        <div class="step-indicator {{ $currentStep == 1 ? 'active' : '' }}">General</div>
        <div class="step-indicator {{ $currentStep == 2 ? 'active' : '' }}">Location</div>
        <div class="step-indicator {{ $currentStep == 3 ? 'active' : '' }}">Additional</div>
    </div>

    <!-- Step Content -->
    @if ($currentStep == 1)
        <!-- General Information -->
        <div>
            <input type="text" wire:model="name" placeholder="Name">
            @error('name') <span>{{ $message }}</span> @enderror

            <input type="email" wire:model="email" placeholder="Email">
            @error('email') <span>{{ $message }}</span> @enderror

            <input type="password" wire:model="password" placeholder="Password">
            @error('password') <span>{{ $message }}</span> @enderror

            <input type="password" wire:model="password_confirmation" placeholder="Confirm Password">
            @error('password_confirmation') <span>{{ $message }}</span> @enderror
        </div>
    @elseif ($currentStep == 2)
        <!-- Dependent Information -->
        <div>
            <input type="text" wire:model="dependent_full_name" placeholder="Dependent Full Name">
            @error('dependent_full_name') <span>{{ $message }}</span> @enderror

            <input type="text" wire:model="dependent_relationship" placeholder="Relationship">
            @error('dependent_relationship') <span>{{ $message }}</span> @enderror

            <input type="number" wire:model="dependent_age" placeholder="Dependent Age">
            @error('dependent_age') <span>{{ $message }}</span> @enderror

            <input type="text" wire:model="dependent_ic_number" placeholder="Dependent IC Number">
            @error('dependent_ic_number') <span>{{ $message }}</span> @enderror
        </div>
    @elseif ($currentStep == 3)
        <!-- Invoice and Payment -->
        <div>
            <input type="text" wire:model="fund_id" placeholder="Fund ID">
            @error('fund_id') <span>{{ $message }}</span> @enderror

            <input type="number" wire:model="payment_amount" placeholder="Payment Amount">
            @error('payment_amount') <span>{{ $message }}</span> @enderror

            <input type="text" wire:model="payment_status" placeholder="Payment Status">
            @error('payment_status') <span>{{ $message }}</span> @enderror

            <input type="date" wire:model="payment_date" placeholder="Payment Date">
            @error('payment_date') <span>{{ $message }}</span> @enderror
        </div>
    @endif

    <!-- Step Navigation -->
    <div class="mt-4">
        @if ($currentStep > 1)
            <button wire:click="previousStep">Previous</button>
        @endif

        @if ($currentStep < 3)
            <button wire:click="nextStep">Next</button>
        @else
            <button wire:click="submitForm">Submit</button>
        @endif
    </div>
</div>
