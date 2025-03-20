<?php
namespace App\Livewire;

use App\Http\Requests\UserRegistrationRequest;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserRegistration extends Component
{
    // Public properties that will hold the input data
    public $name, $email, $password, $password_confirmation, $ic_number, $age, $phone_number, $home_phone, $address ;
    public $residence_status = '';

    // Validation rules
    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8|confirmed',
        'ic_number' => 'required|numeric|digits:12|unique:users,ic_number',
        'age' => 'required|numeric',
        'phone_number' => 'required|numeric|digits_between:10,11',
        'home_phone' => 'required|numeric|digits_between:10,11',
        'address' => 'required|string',
        'residence_status' => 'required|string',
    ];

    // Error messages
    protected $messages = [
        'name.required' => 'Nama diperlukan.',
        'name.string' => 'Nama mesti berupa teks.',
        'name.max' => 'Nama tidak boleh melebihi 255 aksara.',
        'email.required' => 'Emel diperlukan.',
        'email.email' => 'Emel mesti alamat emel yang sah.',
        'email.unique' => 'Emel telah digunakan.',
        'password.required' => 'Kata laluan diperlukan.',
        'password.min' => 'Kata laluan mesti sekurang-kurangnya 8 aksara.',
        'password.confirmed' => 'Pengesahan kata laluan tidak sepadan.',
        'password_confirmation.required' => 'Pengesahan kata laluan diperlukan.',
        'password_confirmation.min' => 'Pengesahan kata laluan mesti sekurang-kurangnya 8 aksara.',
        'ic_number.required' => 'Nombor IC diperlukan.',
        'ic_number.digits' => 'Nombor IC mesti 12 digit.',
        'ic_number.unique' => 'Nombor IC telah digunakan.',
        'age.required' => 'Umur diperlukan.',
        'age.integer' => 'Umur mesti berupa angka.',
        'age.min' => 'Umur mesti sekurang-kurangnya 18 tahun.',
        'phone_number.required' => 'Nombor telefon diperlukan.',
        'phone_number.numeric' => 'Nombor telefon mesti berupa angka.',
        'phone_number.digits_between' => 'Nombor telefon mesti antara 10 hingga 15 digit.',
        'home_phone.required' => 'Nombor telefon rumah diperlukan.',
        'home_phone.numeric' => 'Nombor telefon rumah mesti berupa angka.',
        'home_phone.digits_between' => 'Nombor telefon rumah mesti antara 10 hingga 15 digit.',
        'address.required' => 'Alamat diperlukan.',
        'address.string' => 'Alamat mesti berupa teks.',
        'address.max' => 'Alamat tidak boleh melebihi 255 aksara.',
        'residence_status.required' => 'Status kediaman diperlukan.',
        'residence_status.in' => 'Status kediaman mesti salah satu daripada: kekal, sewa.',
    ];

    public function submit()
    {
        $validatedData = $this->validate();

        // Get the last 'No_Ahli' and increment it
        $lastUser = User::latest('No_Ahli')->first(); // Get the latest user by No_Ahli

        $lastNoAhli = $lastUser ? $lastUser->No_Ahli : '0000'; // Default to '0000' if no user exists
        $nextNoAhli = str_pad(intval($lastNoAhli) + 1, 4, '0', STR_PAD_LEFT); // Increment and pad with zeros

        // Store user data in session (pass it to next step)
        session()->put('user_data', [
            'No_Ahli' => $nextNoAhli,
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'ic_number' => $validatedData['ic_number'],
            'age' => $validatedData['age'],
            'phone_number' => $validatedData['phone_number'],
            'home_phone' => $validatedData['home_phone'],
            'address' => $validatedData['address'],
            'residence_status' => $validatedData['residence_status'],
        ]);

        // Redirect to Dependent Registration Step
        return $this->redirect('/register/dependent', navigate: true);
    }

    public function render()
    {

        return view('livewire.user-registration')->layout('layouts.guest');
    }
}
