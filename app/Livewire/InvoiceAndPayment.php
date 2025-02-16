<?php
// app/Http/Livewire/InvoiceAndPayment.php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Invoice;

class InvoiceAndPayment extends Component
{
    public $fund_id, $payment_amount, $payment_status, $payment_date;

    protected $rules = [
        'fund_id' => 'required|string|max:255',
        'payment_amount' => 'required|numeric|min:1',
        'payment_status' => 'required|string|max:255',
        'payment_date' => 'required|date',
    ];

    public function submit()
    {
        $this->validate();

        // Store payment data in session or database
        $invoice = Invoice::create([
            'fund_id' => $this->fund_id,
            'amount' => $this->payment_amount,
            'status' => $this->payment_status,
            'payment_date' => $this->payment_date,
        ]);

        // Optionally create a new user and dependent, or process the data
        $userData = session()->get('user_data');
        $dependentData = session()->get('dependent_data');

        // Create User and Dependent
        $user = User::create($userData);
        $dependent = Dependent::create([
            'user_id' => $user->id,
            'full_name' => $dependentData['dependent_full_name'],
            'relationship' => $dependentData['dependent_relationship'],
            'age' => $dependentData['dependent_age'],
            'ic_number' => $dependentData['dependent_ic_number'],
        ]);

        // Flash success message
        session()->flash('message', 'Registration and Payment Successful!');

        // Redirect to a success page
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.invoice-and-payment')-> layout('layouts.guest');
    }
}
