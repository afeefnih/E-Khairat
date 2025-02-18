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
}
