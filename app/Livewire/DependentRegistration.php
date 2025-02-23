<?php
// app/Http/Livewire/DependentRegistration.php
namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class DependentRegistration extends Component
{

    public function render()
    {
        return view('auth.dependentRegistration')->layout('layouts.guest');
    }

    public function storeDependent()
    {

        $dependent_data = session()->get('dependents');
        $user = session()->get('user');
         // Store the dependent data in the database
         foreach ($dependent_data as $dependent) {
            Dependent::create([
                'No_Ahli' => $dependent['No_Ahli'],
                'full_name' => $dependent['full_name'],
                'relationship' => $dependent['relationship'],
                'age' => $dependent['age'],
                'ic_number' => $dependent['ic_number'],
            ]);
        }

        Auth::login($user); // Login the user

        // Clear the session data
        session()->forget('user');
        session()->forget('dependents');

        return redirect()->route('dashboard');
    }


}
