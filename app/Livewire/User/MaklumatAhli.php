<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MaklumatAhli extends Component
{


    public $state = [];

    public function mount()
    {
        $this->state = Auth::user()->toArray();
    }
    public function render()
    {

        return view('livewire.user.maklumat-ahli');
    }


}
