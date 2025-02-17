<?php


namespace App\Livewire;

use Livewire\Component;

class DependentRegistrationForm extends Component
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

        // Add the new dependent to the session
        $dependentData = [
            'dependent_full_name' => $this->dependent_full_name,
            'dependent_relationship' => $this->dependent_relationship,
            'dependent_age' => $this->dependent_age,
            'dependent_ic_number' => $this->dependent_ic_number,
        ];

        $dependents = session()->get('dependents', []);
        $dependents[] = $dependentData;
        session()->put('dependents', $dependents);

        // Emit event to notify DependentList component to refresh
        $this->dispatch('dependentAdded', $dependentData);

        // Clear the form
        $this->resetForm();

        session()->flash('message', 'Dependent added successfully!');
    }

    public function resetForm()
    {
        $this->dependent_full_name = '';
        $this->dependent_relationship = '';
        $this->dependent_age = '';
        $this->dependent_ic_number = '';
    }

    public function render()
    {
        return view('livewire.dependent-registration-form');
    }


}
