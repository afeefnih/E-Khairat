<?php

namespace App\Livewire\User;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Dependent;

class MaklumatTanggungan extends Component
{
    public $dependents = [];
    public $dependent; // Add this to hold the current dependent for editing
    public $dependentIdToDelete; // Add this to track which dependent to delete

    public function mount()
    {
        // Fetch dependents for the authenticated user
        $this->dependents = Auth::user()->dependents;
    }

    public function editDependent($dependentId)
    {
        // Fetch the dependent from the database using the dependent_id
        $dependent = Dependent::where('dependent_id', $dependentId)->first();

        if ($dependent) {
            // Set the dependent for editing
            $this->dependent = $dependent;

        }
        $this->isModalOpen = true; // Show the edit modal
    }

    public function setDependentToDelete($dependentId)
    {
        // Set the ID of the dependent to delete
        $this->dependentIdToDelete = $dependentId;
    }

    public function deleteDependent()
    {
        if ($this->dependentIdToDelete) {
            // Find and delete the dependent
            $dependent = Dependent::where('dependent_id', $this->dependentIdToDelete)->first();

            if ($dependent) {
                $dependent->delete();
                // Reset the dependentIdToDelete
                $this->dependentIdToDelete = null;
                // Refresh the dependents list
                $this->dependents = Auth::user()->dependents;
            }
        }
    }

    public function render()
    {
        return view('livewire.user.maklumat-tanggungan');
    }
}
