<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRegistration extends Component
{
    public $name, $email, $password, $password_confirmation, $ic_number, $age, $phone_number, $home_phone, $address, $residence_status;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'nullable|string|email|max:255|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'password_confirmation' => 'required|string|min:8',
        'ic_number' => 'required|numeric|digits:12',
        'age' => 'required|integer|min:18',
        'phone_number' => 'required|numeric|digits_between:10,15',
        'home_phone' => 'nullable|numeric|digits_between:10,15',
        'address' => 'required|string|max:255',
        'residence_status' => 'required|in:kekal,sewa',
    ];

    public function submit()
    {
        $this->validate();

        // Get the last 'No_Ahli' and increment it
    $lastUser = User::latest('id')->first();  // Get the latest user by ID

    $lastNoAhli = $lastUser ? $lastUser->No_Ahli : '0000';  // Default to '0000' if no user exists
    $nextNoAhli = str_pad((intval($lastNoAhli) + 1), 4, '0', STR_PAD_LEFT);  // Increment and pad with zeros


        // Store user data in session (pass it to next step)
        session()->put('user_data', [
            'No_Ahli' => $nextNoAhli,
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

        // Dispatch an event to notify other components (if needed) and redirect
        $this->dispatch('userRegistered');  // Correct dispatch for Livewire 3.x

        // Redirect to Dependent Registration Step
       return $this-> redirect('/register/dependent',navigate: true);
    }

    public function render()
    {
        return view('livewire.user-registration')->layout('layouts.guest');
    }
}
