<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class MaklumatAhli extends Component
{
    public $editMode = false;
    public $state = [];


    public function toggleEditMode()
    {
        $this->editMode = !$this->editMode;
    }

    public function cancelEdit()
    {
        $this->editMode = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->state = Auth::user()->withoutRelations()->toArray();
    }

    public function mount()
    {
        $this->state = Auth::user()->toArray();

    }

    public function updateProfileInformation()
    {
        $rules = [
            'state.name' => 'required|string|max:255',
            'state.email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore(Auth::id())],
            'state.ic_number' => ['required', 'string', 'max:255', Rule::unique('users', 'ic_number')->ignore(Auth::id())],
            'state.age' => 'required|numeric|min:1|max:150',
            'state.phone_number' => 'required|string|max:255',
            'state.home_phone' => 'required|string|max:255',
            'state.address' => 'required|string|max:255',
            'state.residence_status' => ['required', 'string', Rule::in(['kekal', 'sewa'])],
        ];

        $message = [
            'state.name.required' => 'Nama diperlukan.',
            'state.name.string' => 'Nama mesti berupa teks.',
            'state.name.max' => 'Nama tidak boleh melebihi 255 aksara.',
            'state.email.required' => 'Emel diperlukan.',
            'state.email.email' => 'Emel mesti alamat emel yang sah.',
            'state.email.max' => 'Emel tidak boleh melebihi 255 aksara.',
            'state.ic_number.required' => 'Nombor IC diperlukan.',
            'state.ic_number.string' => 'Nombor IC mesti berupa teks.',
            'state.ic_number.max' => 'Nombor IC tidak boleh melebihi 255 aksara.',
            'state.age.required' => 'Umur diperlukan.',
            'state.age.numeric' => 'Umur mesti berupa nombor.',
            'state.age.min' => 'Umur tidak boleh kurang daripada 1.',
            'state.age.max' => 'Umur tidak boleh melebihi 150.',
            'state.phone_number.required' => 'Nombor telefon diperlukan.',
            'state.phone_number.string' => 'Nombor telefon mesti berupa teks.',
            'state.phone_number.max' => 'Nombor telefon tidak boleh melebihi 255 aksara.',
            'state.home_phone.required' => 'Nombor telefon rumah diperlukan.',
            'state.home_phone.string' => 'Nombor telefon rumah mesti berupa teks.',
            'state.home_phone.max' => 'Nombor telefon rumah tidak boleh melebihi 255 aksara.',
            'state.address.required' => 'Alamat diperlukan.',
            'state.address.string' => 'Alamat mesti berupa teks.',
            'state.address.max' => 'Alamat tidak boleh melebihi 255 aksara.',
            'state.residence_status.required' => 'Status kediaman diperlukan.',
            'state.residence_status.string' => 'Status kediaman mesti berupa teks.',
            'state.residence_status.in' => 'Status kediaman mesti salah satu daripada: kekal, sewa.',
        ];

        $this->validate($rules, $message);

        $user = Auth::user();

        $user->update([
            'name' => $this->state['name'],
            'email' => $this->state['email'],
            'ic_number' => $this->state['ic_number'],
            'age' => $this->state['age'],
            'phone_number' => $this->state['phone_number'],
            'home_phone' => $this->state['home_phone'],
            'address' => $this->state['address'],
            'residence_status' => $this->state['residence_status'],
        ]);


        $this->editMode = false;


    }
    public function render()
    {
        return view('livewire.user.maklumat-ahli');
    }
}
