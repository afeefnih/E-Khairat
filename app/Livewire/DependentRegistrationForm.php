<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Dependent;
use Illuminate\Validation\Rule;

class DependentRegistrationForm extends Component
{
    public $dependent_full_name, $dependent_relationship = '', $dependent_age, $dependent_ic_number;

    protected $rules = [
        'dependent_full_name' => 'required|string|max:255',
        'dependent_relationship' => 'required|string',
        'dependent_age' => 'required|numeric',
        'dependent_ic_number' => 'required|numeric|digits:12',
    ];

    protected $messages = [
        'dependent_full_name.required' => 'Nama penuh diperlukan.',
        'dependent_full_name.string' => 'Nama penuh mesti berupa teks.',
        'dependent_full_name.max' => 'Nama penuh tidak boleh melebihi 255 aksara.',
        'dependent_relationship.required' => 'Hubungan diperlukan.',
        'dependent_relationship.string' => 'Hubungan mesti berupa teks.',
        'dependent_age.required' => 'Umur diperlukan.',
        'dependent_age.numeric' => 'Umur mesti berupa angka.',
        'dependent_ic_number.required' => 'Nombor IC diperlukan.',
        'dependent_ic_number.numeric' => 'Nombor IC mesti berupa angka.',
        'dependent_ic_number.digits' => 'Nombor IC mesti 12 digit.',
    ];


    public function submit()
    {

        $this->validate();

        // Create a new dependent
        $dependentData = [
            'No_Ahli' => session()->get('user_data')['No_Ahli'],
            'full_name' => $this->dependent_full_name,
            'relationship' => $this->dependent_relationship,
            'age' => $this->dependent_age,
            'ic_number' => $this->dependent_ic_number,
        ];

        // Store the new dependent in the session
        $dependents = session()->get('dependents', []);
        $dependents[] = $dependentData;
        session()->put('dependents', $dependents);

        // Emit event to update DependentList in parent component
        $this->dispatch('dependentAdded');
        session()->flash('message', 'Dependent added successfully!');

        // Reset form
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->dependent_full_name = '';
        $this->dependent_relationship = '';  // Reset to empty string so it defaults to "Pilih Hubungan"
        $this->dependent_age = '';
        $this->dependent_ic_number = '';
    }

    public function render()
    {
        return view('livewire.dependent-registration-form');
    }
}


