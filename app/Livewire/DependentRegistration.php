<?php
// app/Http/Livewire/DependentRegistration.php
namespace App\Livewire;

use Livewire\Component;

class DependentRegistration extends Component
{
    public $dependent_full_name, $dependent_relationship, $dependent_age, $dependent_ic_number;
    public $dependents = [];
    public $editDependentId = null;

    protected $rules = [
        'dependent_full_name' => 'required|string|max:255',
        'dependent_relationship' => 'required|string|max:255',
        'dependent_age' => 'required|integer|min:0',
        'dependent_ic_number' => 'required|numeric|digits:12',
    ];

    public function mount()
    {
        // Load existing dependents from the session (if any)
        $this->dependents = session()->get('dependents', []);
    }

    public function submit()
    {
        $this->validate();

        // Prepare the dependent data to be stored
        $dependentData = [
            'dependent_full_name' => $this->dependent_full_name,
            'dependent_relationship' => $this->dependent_relationship,
            'dependent_age' => $this->dependent_age,
            'dependent_ic_number' => $this->dependent_ic_number,
        ];

        // If we're editing an existing dependent, update it
        if ($this->editDependentId !== null) {
            $dependents = session()->get('dependents', []);
            $dependents[$this->editDependentId] = $dependentData;
            session()->put('dependents', $dependents);
            session()->flash('message', 'Dependent updated successfully!');
        } else {
            // Store new dependent in session
            $dependents = session()->get('dependents', []);
            $dependents[] = $dependentData;
            session()->put('dependents', $dependents);
            session()->flash('message', 'Dependent added successfully!');
        }

        // Clear the form for the next entry
        $this->resetForm();
    }

    public function editDependent($index)
    {
        // Get the dependent data from the session using the index
        $dependents = session()->get('dependents', []);
        $dependent = $dependents[$index] ?? null;

        if ($dependent) {
            $this->dependent_full_name = $dependent['dependent_full_name'];
            $this->dependent_relationship = $dependent['dependent_relationship'];
            $this->dependent_age = $dependent['dependent_age'];
            $this->dependent_ic_number = $dependent['dependent_ic_number'];
            $this->editDependentId = $index; // Store the index to know which dependent to edit
        }
    }

    public function deleteDependent($index)
    {
        // Remove the dependent from session by its index
        $dependents = session()->get('dependents', []);
        unset($dependents[$index]);

        // Reindex the array to avoid gaps
        $dependents = array_values($dependents);
        session()->put('dependents', $dependents);
        session()->flash('message', 'Dependent deleted successfully!');
    }

    public function resetForm()
    {
        $this->dependent_full_name = '';
        $this->dependent_relationship = '';
        $this->dependent_age = '';
        $this->dependent_ic_number = '';
        $this->editDependentId = null; // Reset the edit flag
    }

    public function render()
    {
        return view('livewire.dependent-registration')-> layout('layouts.guest');
    }
}
