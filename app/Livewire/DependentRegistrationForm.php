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

        // Create a new dependent
        $dependentData = [
            'dependent_full_name' => $this->dependent_full_name,
            'dependent_relationship' => $this->dependent_relationship,
            'dependent_age' => $this->dependent_age,
            'dependent_ic_number' => $this->dependent_ic_number,
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
        $this->dependent_relationship = '';
        $this->dependent_age = '';
        $this->dependent_ic_number = '';
    }

    public function render()
    {
        return view('livewire.dependent-registration-form');
    }
}
