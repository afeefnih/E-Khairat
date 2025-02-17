<?php
// app/Http/Livewire/DependentList.php
namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class DependentList extends Component
{
    public $dependents = [];

    // Listen for the dependentAdded event to refresh the list
    protected $listeners = ['dependentAdded' => 'refreshDependents', 'dependentDeleted' => 'refreshDependents'];

    public function submit()
    {
        // This could be used to save the dependents to the database
        // For now, we will just store them in the session
        session()->put('dependents', $this->dependents);

        // Redirect to the next step
        return redirect()->route('register.invoice');
    }

    public function mount()
    {
        $this->refreshDependents();
    }

    public function refreshDependents()
    {
        // Load dependents from the session
        $this->dependents = session()->get('dependents', []);
    }

    public function deleteDependent($index)
    {
        // Get the dependents from the session
        $dependents = session()->get('dependents', []);

        // Remove the dependent
        unset($dependents[$index]);

        // Reindex the array to avoid gaps
        $dependents = array_values($dependents);
        session()->put('dependents', $dependents);

        // Emit event to refresh the list after deletion
        $this->emit('dependentDeleted');

        session()->flash('message', 'Dependent deleted successfully!');
    }

    public function editDependent($index)
    {
        // This could load the dependent data for editing
        $dependent = $this->dependents[$index];
        $this->emit('dependentEdit', $dependent, $index);
    }

    #[On('dependentCreated')]
    public function updateList( $dependentData)
    {
        $this->dependents[] = $dependentData;

    }

    public function render()
    {
        return view('livewire.dependent-list');
    }
}
