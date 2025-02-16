<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Dependent;
use App\Models\Invoice;
use Illuminate\Support\Facades\Hash;
use Laravel\Jetstream\Jetstream;

class MultiStepRegistration extends Component
{
    public $currentStep = 1;

    // Step 1 - User Registration Fields
    public $name, $email, $password, $password_confirmation, $ic_number, $age, $phone_number, $home_phone, $address, $residence_status;

    // Step 2 - Dependent Registration Fields
    public $dependent_full_name, $dependent_relationship, $dependent_age, $dependent_ic_number;

    // Step 3 - Payment Fields
    public $fund_id, $payment_amount, $payment_status, $payment_date;

    // Validation rules
    protected $rules = [
        // Step 1 Validation Rules
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'password_confirmation' => 'required|string|min:8',
        'ic_number' => 'required|numeric|digits:12',
        'age' => 'required|integer|min:18',
        'phone_number' => 'required|numeric|digits_between:10,15',
        'home_phone' => 'nullable|numeric|digits_between:10,15',
        'address' => 'required|string|max:255',
        'residence_status' => 'required|in:kekal,sewa',

        // Step 2 Validation Rules
        'dependent_full_name' => 'required|string|max:255',
        'dependent_relationship' => 'required|string|max:255',
        'dependent_age' => 'required|integer|min:0',
        'dependent_ic_number' => 'required|numeric|digits:12',

        // Step 3 Validation Rules
        'fund_id' => 'required|string|max:255',
        'payment_amount' => 'required|numeric|min:1',
        'payment_status' => 'required|string|max:255',
        'payment_date' => 'required|date',
    ];

    public function nextStep()
    {
        // Validate the current step before moving to the next
        $this->validate();

        if ($this->currentStep < 3) {
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function submitForm()
    {
        // Validate the last step
        $this->validate();

        // Step 1: Create user
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'ic_number' => $this->ic_number,
            'age' => $this->age,
            'phone_number' => $this->phone_number,
            'home_phone' => $this->home_phone,
            'address' => $this->address,
            'residence_status' => $this->residence_status,
        ]);

        // Step 2: Create dependent
        $dependent = Dependent::create([
            'user_id' => $user->id,
            'full_name' => $this->dependent_full_name,
            'relationship' => $this->dependent_relationship,
            'age' => $this->dependent_age,
            'ic_number' => $this->dependent_ic_number,
        ]);

        // Step 3: Create invoice
        $invoice = Invoice::create([
            'user_id' => $user->id,
            'fund_id' => $this->fund_id,
            'amount' => $this->payment_amount,
            'status' => $this->payment_status,
            'payment_date' => $this->payment_date,
        ]);

        session()->flash('message', 'Registration and Payment Successful!');
        return redirect()->route('home');
    }

    public function render()
    {
        return view('livewire.multi-step-registration')->layout('layouts.guest');
    }

}
