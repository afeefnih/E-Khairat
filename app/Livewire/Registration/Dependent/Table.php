<?php

namespace App\Livewire\Registration\Dependent;

use Livewire\Component;

class Table extends Component
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

    // Listen for the dependentAdded and dependentDeleted events to refresh the list
    protected $listeners = ['dependentAdded' => 'refreshDependents', 'dependentDeleted' => 'refreshDependents'];

    // Function to store dependents and redirect
    public function submit()
    {
        session()->put('dependents', $this->dependents);
        return redirect()->route('register.invoice');
    }

    // Fetch the dependents from the session when the component is mounted
    public function mount()
    {
        $this->refreshDependents();
    }

    // Function to refresh dependents list
    public function refreshDependents()
    {
        $this->dependents = session()->get('dependents', []);
    }
    // Method to set the dependent to delete and open the modal
    public function setDependentToDelete($dependentId)
    {
        // Set the dependent ID that we want to delete
        $this->dependentToDelete = $dependentId;
        $this->isDeleteModalOpen = true;  // Open the delete confirmation modal
    }


   // Method to delete the dependent
   public function deleteDependent()
   {
       if (is_null($this->dependentToDelete)) {
           return;
       }

       // Get the dependents from the session
       $dependents = session()->get('dependents', []);

       // Remove the dependent
       unset($dependents[$this->dependentToDelete]);

       // Reindex the array to avoid gaps
       $dependents = array_values($dependents);
       session()->put('dependents', $dependents);

       // Close the modal
       $this->isDeleteModalOpen = false;

       // Emit event to refresh the list after deletion
       $this->dispatch('dependentDeleted');
       session()->flash('message', 'Dependent deleted successfully!');
   }

    // Method to close the delete modal
    public function closeDeleteModal()
    {
        $this->isDeleteModalOpen = false;
    }

    // Function to open the edit modal and set the dependent to be edited
    public function editDependent($index)
    {
        $dependent = $this->dependents[$index];

        $this->dependent_full_name = $dependent['full_name'];
        $this->dependent_relationship = $dependent['relationship'];
        $this->dependent_age = $dependent['age'];
        $this->dependent_ic_number = $dependent['ic_number'];
        $this->editDependentId = $index;
        $this->isModalOpen = true; // Show the edit modal
    }

    // Function to handle the submission of the edited dependent
    public function submitEdit()
    {
        $this->validate();

        // Update the dependent in the session
        $this->dependents[$this->editDependentId] = [
            'dependent_full_name' => $this->dependent_full_name,
            'dependent_relationship' => $this->dependent_relationship,
            'dependent_age' => $this->dependent_age,
            'dependent_ic_number' => $this->dependent_ic_number,
        ];

        // Store the updated dependents in session
        session()->put('dependents', $this->dependents);
        $this->isModalOpen = false; // Close the modal after submitting

        session()->flash('message', 'Dependent updated successfully!');
    }

    // Event listener for when a dependent is created and added to the list
    public function updateList($dependentData)
    {
        $this->dependents[] = $dependentData;
    }

    // Store dependents in the session and redirect to invoice page
    public function saveDependents()
    {
        // Store all dependents in the session
        session()->put('dependents', $this->dependents);

        $this->dispatch('redirectToInvoice'); // Dispatch a browser event to redirect to the invoice page
        session()->flash('message', 'User registration is complete. You can now add dependents!');
        // Instead of using a traditional redirect, use Livewire's redirect method:
        return $this-> redirect('/register/invoice',navigate: true);
    }

    public function render()
    {
        return view('livewire.registration.dependent.table');
    }
}
