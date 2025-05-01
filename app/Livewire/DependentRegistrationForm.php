<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Dependent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DependentRegistrationForm extends Component
{
    public $dependent_full_name, $dependent_relationship = '', $dependent_age, $dependent_ic_number;

    protected $rules = [
        'dependent_full_name' => 'required|string|max:255',
        'dependent_relationship' => 'required|string',
        'dependent_age' => 'required|numeric',
        'dependent_ic_number' => 'required|numeric|digits:12|unique:dependents,ic_number',
    ];

    protected $messages = [
        'dependent_full_name.required' => 'Nama penuh diperlukan.',
        'dependent_full_name.string' => 'Nama penuh mesti berupa teks.',
        'dependent_full_name.max' => 'Nama penuh tidak boleh melebihi 255 aksara.',
        'dependent_relationship.required' => 'Hubungan diperlukan.',
        'dependent_relationship.string' => 'Hubungan mesti berupa teks.',
        'dependent_age.required' => 'Umur diperlukan.',
        'dependent_age.numeric' => 'Umur mesti berupa angka.',
        'dependent_ic_number.required' => 'Nombor IC diperlukan.',
        'dependent_ic_number.numeric' => 'Nombor IC mesti berupa angka.',
        'dependent_ic_number.digits' => 'Nombor IC mesti 12 digit.',
        'dependent_ic_number.unique' => 'Nombor IC telah wujud.',
    ];

    // Add updatedDependentIcNumber method to auto-calculate age from IC number
    public function updatedDependentIcNumber($value)
    {
        // Only calculate if 12 digits
        if (preg_match('/^\d{12}$/', $value)) {
            $year = substr($value, 0, 2);
            $month = substr($value, 2, 2);
            $day = substr($value, 4, 2);

            $currentYear = date('Y'); // Get current year dynamically
            $currentMonth = date('m'); // Get current month
            $currentDay = date('d'); // Get current day

            // Determine century based on year digits
            $birthYear = (int)$year;
            if ($birthYear >= 0 && $birthYear <= 30) {
                // Years 00-30 are assumed to be 2000-2030
                $birthYear += 2000;
            } else {
                // Years 31-99 are assumed to be 1931-1999
                $birthYear += 1900;
            }

            // Calculate age
            $age = $currentYear - $birthYear;

            // Check if birthday has occurred this year
            if ($currentMonth < (int)$month || ($currentMonth == (int)$month && $currentDay < (int)$day)) {
                $age--;
            }

            $this->dependent_age = $age;
        } else {
            $this->dependent_age = null;
        }
    }

    public function submit()
    {
        if(Auth::check()){
            $this->addNewDependent();
        } else {
            $this->validate();

            // Create a new dependent
            $dependentData = [
                'No_Ahli' => session()->get('user_data')['No_Ahli'],
                'full_name' => $this->dependent_full_name,
                'relationship' => $this->dependent_relationship,
                'age' => $this->dependent_age,
                'ic_number' => $this->dependent_ic_number,
            ];

            // Store the new dependent in the session
            $dependents = session()->get('dependents', []);
            $dependents[] = $dependentData;
            session()->put('dependents', $dependents);

            // Emit event to update DependentList in parent component
            $this->dispatch('dependentAdded');
            session()->flash('message', 'Tanggungan berjaya ditambah!');

            // Reset form
            $this->resetForm();
        }
    }

    public function addNewDependent()
    {
        $this->validate();

        $dependentData = [
            'full_name' => $this->dependent_full_name,
            'relationship' => $this->dependent_relationship,
            'age' => $this->dependent_age,
            'ic_number' => $this->dependent_ic_number,
        ];

        $dependents = cache()->get('dependents', []);

        // Add the new dependent to the array
        $dependents[] = $dependentData;

        // Store the updated array back in the cache
        cache()->put('dependents', $dependents);

        // Display success message
        session()->flash('message', 'Tanggungan berjaya ditambah!');

        // Reset the form
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->dependent_full_name = '';
        $this->dependent_relationship = '';  // Reset to empty string so it defaults to "Pilih Hubungan"
        $this->dependent_age = '';
        $this->dependent_ic_number = '';
    }

    public function render()
    {
        return view('livewire.dependent-registration-form');
    }
}
