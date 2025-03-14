<?php

namespace App\Livewire;

use Livewire\Component;

class UserDashboard extends Component
{
    public function render()
    {
        return view('livewire.user.user-dashboard',[
            'payments' => auth()->user()->payments()->with('paymentCategory')->latest()->get(),
        ]);
    }
}
