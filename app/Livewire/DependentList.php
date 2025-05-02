<?php
// app/Http/Livewire/DependentList.php
namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Dependent;
use App\Models\DependentEditRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

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
    public $pendingEditRequests = [];
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
        'dependentDeleted' => 'refreshDependents',
        'editRequestApproved' => 'refreshDependents',
    ];

    /**
     * Get all admin users
     */
    /**
 * Get all admin users
 */
protected function getAdmins()
{
    // Using your role system to find admins
    return \App\Models\User::whereHas('roles', function($query) {
        $query->where('name', 'admin');
    })->get();
}

/**
 * Notify all admins about a new request
 */
protected function notifyAdmins(DependentEditRequest $request)
{
    $admins = $this->getAdmins();

    foreach ($admins as $admin) {
        $admin->notify(new \App\Notifications\NewDependentRequest($request));
    }
}

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

            // Load pending edit requests
            $this->pendingEditRequests = DependentEditRequest::where('user_id', Auth::id())->where('status', 'pending')->get();
        } else {
            // If user is not logged in, fetch from session
            $this->dependents = session()->get('dependents', []);
            $this->pendingDependents = [];
            $this->pendingEditRequests = [];
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

            // Check if there's already a pending edit request for this dependent
            $existingRequest = DependentEditRequest::where('dependent_id', $index)->where('user_id', Auth::id())->where('status', 'pending')->first();

            if ($existingRequest) {
                session()->flash('info', 'You already have a pending edit request for this dependent.');
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
     * Handle the submission of a new dependent (requires admin approval)
     */
    public function submitPending()
    {
        $this->validate();

        if (Auth::check()) {
            // Create a new addition request in the database
            $request = DependentEditRequest::create([
                'user_id' => Auth::id(),
                'dependent_id' => null,
                'full_name' => $this->dependent_full_name,
                'relationship' => $this->dependent_relationship,
                'age' => $this->dependent_age,
                'ic_number' => $this->dependent_ic_number,
                'status' => 'pending',
                'request_type' => 'add',
            ]);

            // Notify admins about the new request
            $this->notifyAdmins($request);

            $this->resetFormFields();
            $this->isModalOpen = false;
            $this->refreshDependents();

            session()->flash('message', 'Permintaan tambah tanggungan telah dibuat. Sila tunggu kelulusan admin.');
        } else {
            // For non-logged in users, continue with the current flow
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
    }

    /**
     * Handle the submission of edited dependent
     * For logged-in users, this now creates an edit request instead of direct update
     */
    public function submitEdit()
    {
        $this->validate();

        if (Auth::check()) {
            // Create edit request for authenticated users instead of updating directly
            $dependent = Dependent::where('dependent_id', $this->editDependentId)->first();

            if (!$dependent) {
                session()->flash('error', 'Dependent not found!');
                return;
            }

            // Check if anything actually changed
            if ($dependent->full_name == $this->dependent_full_name &&
                $dependent->relationship == $this->dependent_relationship &&
                $dependent->age == $this->dependent_age &&
                $dependent->ic_number == $this->dependent_ic_number) {

                $this->resetFormFields();
                $this->isModalOpen = false;
                session()->flash('info', 'No changes were made to the dependent.');
                return;
            }

            // Check if there's already a pending edit request for this dependent
            $existingRequest = DependentEditRequest::where('dependent_id', $this->editDependentId)
                ->where('user_id', Auth::id())
                ->where('status', 'pending')
                ->first();

            if ($existingRequest) {
                // Update the existing request - no need to notify again
                $existingRequest->update([
                    'full_name' => $this->dependent_full_name,
                    'relationship' => $this->dependent_relationship,
                    'age' => $this->dependent_age,
                    'ic_number' => $this->dependent_ic_number,
                    'request_type' => 'edit',
                ]);
            } else {
                // Create a new edit request
                $request = DependentEditRequest::create([
                    'user_id' => Auth::id(),
                    'dependent_id' => $this->editDependentId,
                    'full_name' => $this->dependent_full_name,
                    'relationship' => $this->dependent_relationship,
                    'age' => $this->dependent_age,
                    'ic_number' => $this->dependent_ic_number,
                    'status' => 'pending',
                    'request_type' => 'edit',
                ]);

                // Notify admins
                $this->notifyAdmins($request);
            }

            $this->resetFormFields();
            $this->isModalOpen = false;
            $this->refreshDependents();

            session()->flash('message', 'Permintaan kemaskini tanggungan telah dibuat. Sila tunggu kelulusan admin.');
        } else {
            // Update in session for guests (no approval needed)
            $this->dependents[$this->editDependentId] = [
                'full_name' => $this->dependent_full_name,
                'relationship' => $this->dependent_relationship,
                'age' => $this->dependent_age,
                'ic_number' => $this->dependent_ic_number,
            ];

            session()->put('dependents', $this->dependents);

            $this->resetFormFields();
            $this->isModalOpen = false;
            $this->refreshDependents();

            session()->flash('message', 'Dependent updated successfully!');
        }
    }

    /**
     * Handle the deletion request for a dependent (requires admin approval)
     */
    public function requestDelete($dependentId)
{
    if (Auth::check()) {
        // Find the dependent
        $dependent = Dependent::where('dependent_id', $dependentId)->first();

        if (!$dependent) {
            session()->flash('error', 'Tanggungan tidak dijumpai!');
            return;
        }

        // Check if there's already a pending delete request for this dependent
        $existingRequest = DependentEditRequest::where('dependent_id', $dependentId)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->where('request_type', 'delete')
            ->first();

        if ($existingRequest) {
            session()->flash('info', 'Permintaan padam untuk tanggungan ini sudah wujud dan sedang menunggu kelulusan.');
            return;
        }

        // Create a delete request
        $request = DependentEditRequest::create([
            'user_id' => Auth::id(),
            'dependent_id' => $dependentId,
            'full_name' => $dependent->full_name,
            'relationship' => $dependent->relationship,
            'age' => $dependent->age,
            'ic_number' => $dependent->ic_number,
            'status' => 'pending',
            'request_type' => 'delete',
        ]);

        // Notify admins
        $this->notifyAdmins($request);

        $this->refreshDependents();

        session()->flash('message', 'Permintaan padam tanggungan telah dibuat. Sila tunggu kelulusan admin.');
        } else {
            // For non-logged in users, continue with immediate deletion
            unset($this->dependents[$dependentId]);
            session()->put('dependents', $this->dependents);
            $this->refreshDependents();

            session()->flash('message', 'Tanggungan telah dipadam.');
        }
    }

    /**
     * Cancel a pending edit request
     */
    public function cancelEditRequest($requestId)
    {
        $request = DependentEditRequest::where('id', $requestId)->where('user_id', Auth::id())->where('status', 'pending')->first();

        if ($request) {
            $request->delete();
            $this->refreshDependents();
            session()->flash('message', 'Permintaan kemaskini tanggungan telah dibatalkan.');
        }
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
     * Delete the selected dependent or create a delete request
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

                session()->flash('message', 'Permintaan tanggungan telah dipadam.');
            }
        } else {
            // Delete from regular list
            if (Auth::check()) {
                // Create delete request instead of immediate deletion
                $this->requestDelete($this->dependentToDelete);
            } else {
                // Delete from session for guests
                $dependents = session()->get('dependents', []);
                unset($dependents[$this->dependentToDelete]);
                session()->put('dependents', $dependents);

                $this->dispatch('dependentDeleted');

                session()->flash('message', 'Tanggungan telah dipadam.');
            }
        }

        $this->isDeleteModalOpen = false;
        $this->refreshDependents();
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
     * Reset form fields after submission
     */
    private function resetFormFields()
    {
        $this->reset(['dependent_full_name', 'dependent_relationship', 'dependent_age', 'dependent_ic_number', 'editDependentId', 'editPendingIndex']);
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.dependent-list');
    }

    public function updatedDependentIcNumber($value)
    {
        // Only calculate if 12 digits
        if (preg_match('/^\d{12}$/', $value)) {
            $year = substr($value, 0, 2);
            $month = substr($value, 2, 2);
            $day = substr($value, 4, 2);
            $currentYear = date('Y');
            $currentMonth = date('m');
            $currentDay = date('d');
            $birthYear = (int)$year + ((int)$year > ($currentYear % 100) ? 1900 : 2000);
            $age = $currentYear - $birthYear;
            $this->dependent_age = $age;
        } else {
            $this->dependent_age = null;
        }
    }
}
