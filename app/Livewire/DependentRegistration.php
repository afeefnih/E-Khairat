<?php

// app/Http/Livewire/DependentRegistration.php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Dependent;

class DependentRegistration extends Component
{
    public $dependent_full_name, $dependent_relationship, $dependent_age, $dependent_ic_number;

    protected $rules = [
        'dependent_full_name' => 'required|string|max:255',
        'dependent_relationship' => 'required|string|max:255',
        'dependent_age' => 'required|integer|min:0',
        'dependent_ic_number' => 'required|numeric|digits:12',
    ];

    public function submit()
    {
        $this->validate();

        // Store dependent data in session (pass it to next step)
        session()->put('dependent_data', [
            'dependent_full_name' => $this->dependent_full_name,
            'dependent_relationship' => $this->dependent_relationship,
            'dependent_age' => $this->dependent_age,
            'dependent_ic_number' => $this->dependent_ic_number,
        ]);

        // Redirect to Invoice and Payment Step
        return redirect()->route('register.invoice');
    }

    public function render()
    {
        return view('livewire.dependent-registration')-> layout('layouts.guest');
    }
}
