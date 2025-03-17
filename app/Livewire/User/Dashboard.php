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
    $payments = auth()->user()->payments()->with('paymentCategory')->latest()->get();

    // Calculate total payments
    $totalPayments = $payments->sum('amount');

    return view('livewire.user.dashboard', [
        'payments' => $payments,
        'totalPayments' => $totalPayments
    ]);
}
}
