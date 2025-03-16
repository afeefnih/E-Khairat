<?php

namespace App\Livewire\User;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{

    public $state = [];

public function mount ()
{
    $this->state = Auth::user()->toArray();


}
    public function render()
    {
        return view('livewire.user.dashboard',[
            'payments' => auth()->user()->payments()->with('paymentCategory')->latest()->get(),

        ]);
    }
}
