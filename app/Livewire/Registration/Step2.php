<?php

namespace App\Livewire\Registration;

use Livewire\Component;

class Step2 extends Component
{

    public $dependent_full_name, $dependent_relationship, $dependent_age, $dependent_ic_number;
    public $dependents = [];
    public $editDependentId = null;
    public $isModalOpen = false; // To control modal visibility
    public $isDeleteModalOpen = false; // To control delete modal visibility
    public $dependentToDelete = null; // Store the dependent to delete


    protected $rules = [
        'dependent_full_name' => 'required|string|max:255',
        'dependent_relationship' => 'required|string|max:255',
        'dependent_age' => 'required|integer|min:0',
        'dependent_ic_number' => 'required|numeric|digits:12',
    ];

    protected $messages = [
        'dependent_full_name.required' => 'Nama penuh diperlukan.',
        'dependent_full_name.string' => 'Nama penuh mesti berupa teks.',
        'dependent_full_name.max' => 'Nama penuh tidak boleh melebihi 255 aksara.',
        'dependent_relationship.required' => 'Hubungan diperlukan.',
        'dependent_relationship.string' => 'Hubungan mesti berupa teks.',
        'dependent_relationship.max' => 'Hubungan tidak boleh melebihi 255 aksara.',
        'dependent_age.required' => 'Umur diperlukan.',
        'dependent_age.integer' => 'Umur mesti berupa angka.',
        'dependent_age.min' => 'Umur mesti sekurang-kurangnya 0 tahun.',
        'dependent_ic_number.required' => 'Nombor IC diperlukan.',
        'dependent_ic_number.numeric' => 'Nombor IC mesti berupa angka.',
        'dependent_ic_number.digits' => 'Nombor IC mesti 12 digit.',
        'dependent_ic_number.unique' => 'Nombor IC telah digunakan.',
    ];

    public function submit(){
        $this -> validate( $this -> rules, $this -> messages );



    }


    public function render()
    {
        return view('livewire.registration.step2');
    }
}
