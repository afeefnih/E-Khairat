<?php
// app/Http/Livewire/InvoiceAndPayment.php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Invoice;
use Livewire\Attributes\On;
use Illuminate\Http\Request;
use Toyyibpay;

class InvoiceAndPayment extends Component
{
    public $fund_id, $payment_amount, $payment_status, $payment_date;
    public $user_data;
    public $dependents;
    public $amount = 100;


    public function mount()
    {
        // Get user data from the session
        $this->user_data = session()->get('user_data', []);

        // Get dependent data from the session (if any)
        $this->dependents = session()->get('dependents', []);
    }

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
            'user_name' => $user->name,
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

    public function getbankFPX()
    {
        $data = Toyyibpay::getBanks();
        dd($data);
    }

    public function paymentRegistration()
    {
        // Validate payment amount before proceeding
        $this->validate([
            'amount' => 'required|numeric|min:1',  // Validate that the amount is numeric and greater than 1
        ]);

        // Retrieve user data
        $name = $this->user_data['name'];
        $email = $this->user_data['email'];
        $phone_number = $this->user_data['phone_number'];

        // Calculate the payment amount (convert to cents for Toyyibpay API)
        $amount = $this->amount * 100; // Convert to cents
        $code = config('toyyibpay.category_codes.yuran_khairat'); // Get category code from config

        // Store the amount in session for later use (in case you need to retrieve it later)
        session(['infaq_amount' => $amount]);

        // Prepare the data for the Toyyibpay bill
        $billData = [
            'billName' => 'Pendaftaran Khairat Kematian',
            'billDescription' => 'Infaq of RM ' . $this->amount,
            'billPriceSetting' => 1, // Fixed amount
            'billPayorInfo' => 1, // Set to 1 to allow user to enter their info
            'billAmount' => $amount,
            'billReturnUrl' => route('infaq.callback'), // URL to redirect after payment
            'billExternalReferenceNo' => uniqid(), // Unique reference number
            'billTo' => $name, // User's name
            'billEmail' => $email, // User's email
            'billPhone' => $phone_number, // User's phone number
        ];

        // Make the call to Toyyibpay API to create the bill
        $data = Toyyibpay::createBill($code, (object)$billData);

        // Check if the BillCode is set and generate payment link
        if (isset($data[0]->BillCode)) {
            $bill_code = $data[0]->BillCode;
            $paymentUrl = Toyyibpay::billPaymentLink($bill_code);

            // Redirect to the payment page using the generated payment URL
            return redirect()->away($paymentUrl);
        } else {
            // Return an error message if the payment bill creation failed
            return redirect()->back()->with('error', 'Failed to create payment bill.');
        }
    }

    public function paymentCallback(Request $request)
    {

        $status = $request->input('status_id'); // 1 = success, 0 = failure
        $billCode = $request->input('billcode'); // ToyyibPay bill code
        $amount = session('infaq_amount', 0); // Default to 0 if not found in session


    }

    public function render()
    {
        return view('livewire.invoice-and-payment')-> layout('layouts.guest');
    }
}
