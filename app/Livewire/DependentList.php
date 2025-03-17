<?php
// app/Http/Livewire/DependentList.php
namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Dependent;
use Illuminate\Support\Facades\Cache;

class DependentList extends Component
{
    // Form properties
    public $dependent_full_name;
    public $dependent_relationship;
    public $dependent_age;
    public $dependent_ic_number;

    // Component state
    public $dependents = [];
    public $pendingDependents = [];
    public $editDependentId = null;
    public $editPendingIndex = null;
    public $isModalOpen = false;
    public $isDeleteModalOpen = false;
    public $dependentToDelete = null;
    public $isPendingDelete = false;

    // Validation rules
    protected $rules = [
        'dependent_full_name' => 'required|string|max:255',
        'dependent_relationship' => 'required|string|max:255',
        'dependent_age' => 'required|integer|min:0',
        'dependent_ic_number' => 'required|numeric|digits:12',
    ];

    // Listeners for events
    protected $listeners = [
        'dependentAdded' => 'refreshDependents',
        'dependentDeleted' => 'refreshDependents'
    ];

    /**
     * Initialize component state
     */
    public function mount()
    {
        $this->refreshDependents();
    }

    /**
     * Load dependents from the appropriate source
     */
    public function refreshDependents()
    {
        if (Auth::check()) {
            // If user is logged in, fetch from database
            $this->dependents = Auth::user()->dependents;

            // Load pending dependents from cache with user-specific key
            $this->pendingDependents = Cache::get('dependents_' . Auth::id(), []);
        } else {
            // If user is not logged in, fetch from session
            $this->dependents = session()->get('dependents', []);
            $this->pendingDependents = []; // No pending dependents for guests
        }
    }

    /**
     * Open modal for adding a new dependent
     */
    public function addNewDependent()
    {
        $this->resetFormFields();
        $this->editDependentId = null;
        $this->editPendingIndex = null;
        $this->isModalOpen = true;
    }

    /**
     * Open the edit modal for an existing dependent
     */
    public function editDependent($index)
    {
        if (Auth::check()) {
            $dependent = Dependent::where('dependent_id', $index)->first();

            if (!$dependent) {
                session()->flash('error', 'Dependent not found!');
                return;
            }

            $this->dependent_full_name = $dependent->full_name;
            $this->dependent_relationship = $dependent->relationship;
            $this->dependent_age = $dependent->age;
            $this->dependent_ic_number = $dependent->ic_number;
            $this->editDependentId = $index;
            $this->editPendingIndex = null;
        } else {
            if (!isset($this->dependents[$index])) {
                session()->flash('error', 'Dependent not found!');
                return;
            }

            $dependent = $this->dependents[$index];
            $this->dependent_full_name = $dependent['full_name'];
            $this->dependent_relationship = $dependent['relationship'];
            $this->dependent_age = $dependent['age'];
            $this->dependent_ic_number = $dependent['ic_number'];
            $this->editDependentId = $index;
            $this->editPendingIndex = null;
        }

        $this->isModalOpen = true;
    }

    /**
     * Open the edit modal for a pending dependent
     */
    public function editPendingDependent($index)
    {
        if (isset($this->pendingDependents[$index])) {
            $pending = $this->pendingDependents[$index];
            $this->dependent_full_name = $pending['full_name'];
            $this->dependent_relationship = $pending['relationship'];
            $this->dependent_age = $pending['age'];
            $this->dependent_ic_number = $pending['ic_number'];
            $this->editPendingIndex = $index;
            $this->editDependentId = null;
            $this->isModalOpen = true;
        } else {
            session()->flash('error', 'Pending dependent not found!');
        }
    }

    /**
     * Handle the submission of a new or edited pending dependent
     */
    public function submitPending()
    {
        $this->validate();

        $newDependent = [
            'full_name' => $this->dependent_full_name,
            'relationship' => $this->dependent_relationship,
            'age' => $this->dependent_age,
            'ic_number' => $this->dependent_ic_number,
        ];

        if ($this->editPendingIndex !== null) {
            // Update existing pending dependent
            $this->pendingDependents[$this->editPendingIndex] = $newDependent;
        } else {
            // Add new pending dependent
            $this->pendingDependents[] = $newDependent;
        }

        // Save to cache with user-specific key
        if (Auth::check()) {
            Cache::put('dependents_' . Auth::id(), $this->pendingDependents, now()->addDay());
        }

        $this->resetFormFields();
        $this->isModalOpen = false;

        session()->flash('message', 'Tanggungan ditambahkan ke senarai menunggu.');
    }

    /**
     * Handle the submission of edited dependent
     */
    public function submitEdit()
    {
        $this->validate();

        if (Auth::check()) {
            // Update in database for authenticated users
            $dependent = Dependent::where('dependent_id', $this->editDependentId)->first();

            if (!$dependent) {
                session()->flash('error', 'Dependent not found!');
                return;
            }

            $dependent->update([
                'full_name' => $this->dependent_full_name,
                'relationship' => $this->dependent_relationship,
                'age' => $this->dependent_age,
                'ic_number' => $this->dependent_ic_number,
            ]);
        } else {
            // Update in session for guests
            $this->dependents[$this->editDependentId] = [
                'full_name' => $this->dependent_full_name,
                'relationship' => $this->dependent_relationship,
                'age' => $this->dependent_age,
                'ic_number' => $this->dependent_ic_number,
            ];

            session()->put('dependents', $this->dependents);
        }

        $this->resetFormFields();
        $this->isModalOpen = false;
        $this->refreshDependents();

        session()->flash('message', 'Dependent updated successfully!');
    }

    /**
     * Set up dependent for deletion and open confirmation modal
     */
    public function setDependentToDelete($dependentId)
    {
        $this->dependentToDelete = $dependentId;
        $this->isPendingDelete = false;
        $this->isDeleteModalOpen = true;
    }

    /**
     * Set up pending dependent for deletion and open confirmation modal
     */
    public function removePendingDependent($index)
    {
        $this->dependentToDelete = $index;
        $this->isPendingDelete = true;
        $this->isDeleteModalOpen = true;
    }

    /**
     * Delete the selected dependent
     */
    public function deleteDependent()
    {
        if (is_null($this->dependentToDelete)) {
            return;
        }

        if ($this->isPendingDelete) {
            // Delete from pending list
            if (isset($this->pendingDependents[$this->dependentToDelete])) {
                unset($this->pendingDependents[$this->dependentToDelete]);
                $this->pendingDependents = array_values($this->pendingDependents); // Reindex array

                // Update cache if user is logged in
                if (Auth::check()) {
                    Cache::put('dependents_' . Auth::id(), $this->pendingDependents, now()->addDay());
                }
            }
        } else {
            // Delete from regular list
            if (Auth::check()) {
                // Delete from database for authenticated users
                $dependent = Dependent::where('dependent_id', $this->dependentToDelete)->first();

                if ($dependent) {
                    $dependent->delete();
                }
            } else {
                // Delete from session for guests
                $dependents = session()->get('dependents', []);
                unset($dependents[$this->dependentToDelete]);
                session()->put('dependents', $dependents);

                $this->dispatch('dependentDeleted');
            }
        }

        $this->isDeleteModalOpen = false;
        $this->refreshDependents();

        session()->flash('message', 'Dependent deleted successfully!');
    }

    /**
     * Close the delete confirmation modal
     */
    public function closeDeleteModal()
    {
        $this->isDeleteModalOpen = false;
    }

    /**
     * Save all pending dependents to the database
     */
    public function savePendingDependents()
    {
        if (!Auth::check() || empty($this->pendingDependents)) {
            return;
        }

        $user = Auth::user();

        foreach ($this->pendingDependents as $pending) {
            Dependent::create([
                'user_id' => $user->id,
                'full_name' => $pending['full_name'],
                'relationship' => $pending['relationship'],
                'age' => $pending['age'],
                'ic_number' => $pending['ic_number'],
            ]);
        }

        // Clear pending list and cache
        $this->pendingDependents = [];
        Cache::forget('dependents_' . Auth::id());

        // Refresh the list
        $this->refreshDependents();

        session()->flash('message', 'Semua tanggungan menunggu telah disimpan ke akaun anda.');
    }

    /**
     * Save dependents to session and redirect to invoice page
     */
    public function saveDependents()
    {
        session()->put('dependents', $this->dependents);

        $this->dispatch('redirectToInvoice');
        session()->flash('message', 'User registration is complete. You can now add dependents!');

        return $this->redirect('/register/invoice', navigate: true);
    }

    /**
     * Save cached dependents to database for authenticated user
     */
    public function save()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Check if we have any pending dependents
        if (!empty($this->pendingDependents)) {
            $this->savePendingDependents();
        } else {
            // Original behavior if no pending dependents
            $dependents = Cache::get('dependents', []);
            $user = Auth::user();

            if (!empty($dependents)) {
                foreach ($dependents as $dependent) {
                    Dependent::create([
                        'user_id' => $user->id,
                        'full_name' => $dependent['full_name'],
                        'relationship' => $dependent['relationship'],
                        'age' => $dependent['age'],
                        'ic_number' => $dependent['ic_number'],
                    ]);
                }

                // Clear cache after saving
                Cache::forget('dependents');
                $this->refreshDependents();
            }
        }

        session()->flash('message', 'All dependents saved successfully!');
    }

    /**
     * Store dependents and redirect
     */
    public function submit()
    {
        session()->put('dependents', $this->dependents);
        return redirect()->route('register.invoice');
    }

    /**
     * Reset form fields after submission
     */
    private function resetFormFields()
    {
        $this->reset([
            'dependent_full_name',
            'dependent_relationship',
            'dependent_age',
            'dependent_ic_number',
            'editDependentId',
            'editPendingIndex'
        ]);
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.dependent-list');
    }
}
