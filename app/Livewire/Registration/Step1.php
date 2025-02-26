<?php

namespace App\Livewire\Registration;

use Livewire\Component;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;

class Step1 extends Component
{

    public $name, $email, $password, $password_confirmation, $ic_number, $age, $phone_number, $home_phone, $address, $residence_status = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'nullable|string|email|max:255|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'password_confirmation' => 'required|string|min:8',
        'ic_number' => 'required|numeric|digits:12|unique:users,ic_number',
        'age' => 'required|integer|min:18',
        'phone_number' => 'required|numeric|digits_between:10,15',
        'home_phone' => 'required|numeric|digits_between:10,15', // Make home_phone nullable
        'address' => 'required|string|max:255',
        'residence_status' => 'required|in:kekal,sewa',
    ];

    protected $messages = [
        'name.required' => 'Nama diperlukan.',
        'name.string' => 'Nama mesti berupa teks.',
        'name.max' => 'Nama tidak boleh melebihi 255 aksara.',
        'email.string' => 'Emel mesti berupa teks.',
        'email.email' => 'Emel mesti alamat emel yang sah.',
        'email.max' => 'Emel tidak boleh melebihi 255 aksara.',
        'email.unique' => 'Emel telah digunakan.',
        'password.required' => 'Kata laluan diperlukan.',
        'password.string' => 'Kata laluan mesti berupa teks.',
        'password.min' => 'Kata laluan mesti sekurang-kurangnya 8 aksara.',
        'password.confirmed' => 'Pengesahan kata laluan tidak sepadan.',
        'password_confirmation.required' => 'Pengesahan kata laluan diperlukan.',
        'password_confirmation.string' => 'Pengesahan kata laluan mesti berupa teks.',
        'password_confirmation.min' => 'Pengesahan kata laluan mesti sekurang-kurangnya 8 aksara.',
        'ic_number.required' => 'Nombor IC diperlukan.',
        'ic_number.numeric' => 'Nombor IC mesti berupa angka.',
        'ic_number.digits' => 'Nombor IC mesti 12 digit.',
        'ic_number.unique' => 'Nombor IC telah digunakan.',
        'age.required' => 'Umur diperlukan.',
        'age.integer' => 'Umur mesti berupa angka.',
        'age.min' => 'Umur mesti sekurang-kurangnya 18 tahun.',
        'phone_number.required' => 'Nombor telefon diperlukan.',
        'phone_number.numeric' => 'Nombor telefon mesti berupa angka.',
        'phone_number.digits_between' => 'Nombor telefon mesti antara 10 hingga 15 digit.',
        'home_phone.numeric' => 'Nombor telefon rumah mesti berupa angka.',
        'home_phone.digits_between' => 'Nombor telefon rumah mesti antara 10 hingga 15 digit.',
        'home_phone.required'=> 'Nombor telefon rumah diperlukan.',
        'address.required' => 'Alamat diperlukan.',
        'address.string' => 'Alamat mesti berupa teks.',
        'address.max' => 'Alamat tidak boleh melebihi 255 aksara.',
        'residence_status.required' => 'Status kediaman diperlukan.',
        'residence_status.in' => 'Status kediaman mesti salah satu daripada: kekal, sewa.',
    ];




    #[On('step1Listen')]
    public function submit($user = null)
    {
        $this->validate($this->rules, $this->messages);
        session (['user_data' => $this->toArray()]);

        dd(session()->get('user_data'));
        $this->dispatch('step1Complete', [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'ic_number' => $this->ic_number,
            'age' => $this->age,
            'phone_number' => $this->phone_number,
            'home_phone' => $this->home_phone,
            'address' => $this->address,
            'residence_status' => $this->residence_status,
        ]);
    }


    public function render()
    {
        return view('livewire.registration.step1');
    }
}
