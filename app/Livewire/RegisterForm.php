<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Facades\Log;

class RegisterForm extends Component
{
    public $name, $ic_number, $age, $phone_number, $home_phone, $address, $residence_status, $email, $password, $password_confirmation, $terms;

    // Validation rules for the form fields
    protected $rules = [
        'name' => 'required|string|max:255',
        'ic_number' => 'required|numeric|digits:12',
        'age' => 'required|integer|min:18',
        'phone_number' => 'required|numeric|digits_between:10,15',
        'home_phone' => 'nullable|numeric|digits_between:10,15',
        'address' => 'required|string|max:255',
        'residence_status' => 'required|in:kekal,sewa',
        'email' => 'nullable|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'password_confirmation' => 'required|string|min:8',
        'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
    ];

    public function submit()
{

    $this->validate();
    try {
        User::create([
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

        session()->flash('message', 'Registration successful!');


        return redirect()->route('login');
    } catch (\Exception $e) {
        Log::error('Error creating user: ' . $e->getMessage());
        session()->flash('error', 'Something went wrong');
    }
}

    public function render()
    {
        return view('livewire.register-form');
    }
}
