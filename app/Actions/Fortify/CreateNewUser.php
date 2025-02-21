<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
        'name' => 'required|string|max:255',
        'email' => 'nullable|string|email|max:255|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'password_confirmation' => 'required|string|min:8',
        'ic_number' => 'required|numeric|digits:12',
        'age' => 'required|integer|min:18',
        'phone_number' => 'required|numeric|digits_between:10,15',
        'home_phone' => 'nullable|numeric|digits_between:10,15',  // Make home_phone nullable
        'address' => 'required|string|max:255',
        'residence_status' => 'required|in:kekal,sewa',
        ],
       [
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
            'age.required' => 'Umur diperlukan.',
            'age.integer' => 'Umur mesti berupa angka.',
            'age.min' => 'Umur mesti sekurang-kurangnya 18 tahun.',
            'phone_number.required' => 'Nombor telefon diperlukan.',
            'phone_number.numeric' => 'Nombor telefon mesti berupa angka.',
            'phone_number.digits_between' => 'Nombor telefon mesti antara 10 hingga 15 digit.',
            'home_phone.numeric' => 'Nombor telefon rumah mesti berupa angka.',
            'home_phone.digits_between' => 'Nombor telefon rumah mesti antara 10 hingga 15 digit.',
            'address.required' => 'Alamat diperlukan.',
            'address.string' => 'Alamat mesti berupa teks.',
            'address.max' => 'Alamat tidak boleh melebihi 255 aksara.',
            'residence_status.required' => 'Status kediaman diperlukan.',
            'residence_status.in' => 'Status kediaman mesti salah satu daripada: kekal, sewa.',
        ]

        )->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),

        ]);
    }
}
