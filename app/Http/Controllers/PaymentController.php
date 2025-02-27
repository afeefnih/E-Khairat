<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Toyyibpay;
use App\Models\User;
use App\Models\Dependent;
use Illuminate\Support\Facades\Auth;
use App\Models\KhairatKematianFund;
use App\Livewire\UserRegistration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function paymentRegistration(Request $request)
    {
        $user_data = session()->get('user_data'); // Get user data from the session
        $name = $user_data['name'];
        $email = $user_data['email'];
        $phone_number = $user_data['phone_number'];
        $fixedAmount = 100; // Fixed amount for registration

        // Calculate the payment amount (convert to cents for Toyyibpay API)
        $amount = $fixedAmount * 100; // Convert to cents
        $code = config('toyyibpay.category_codes.yuran_khairat'); // Get category code from config

        // Store the amount in session for later use (in case you need to retrieve it later)
        // Prepare the data for the Toyyibpay bill
        $billData = [
            'billName' => 'Pendaftaran Khairat Kematian',
            'billDescription' => 'Infaq of RM ' . $request->amount,
            'billPriceSetting' => 1, // Fixed amount
            'billPayorInfo' => 1, // Set to 1 to allow user to enter their info
            'billAmount' => $amount,
            'billReturnUrl' => route('payment.callback'), // URL to redirect after payment (new callback route)
            'billExternalReferenceNo' => uniqid(), // Unique reference number
            'billTo' => $name, // User's name
            'billEmail' => $email, // User's email
            'billPhone' => $phone_number, // User's phone number
        ];

        // Make the call to Toyyibpay API to create the bill
        $data = Toyyibpay::createBill($code, (object) $billData);

        // Check if the BillCode is set and generate payment link
        if (isset($data[0]->BillCode)) {
            $bill_code = $data[0]->BillCode;
            $paymentUrl = Toyyibpay::billPaymentLink($bill_code);

            // Redirect to the payment page using the generated payment URL
            return redirect()->away($paymentUrl);
        } else {
            // Return an error message if the payment bill creation failed
            return response()->json(['error' => 'Failed to create payment bill.'], 500);
        }
    }



}
