<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Toyyibpay;
use App\Models\User;
//use App\Models\KhairatKematianFund;
use App\Models\Dependent;
use Illuminate\Support\Facades\Auth;
use App\Models\KhairatKematianFund;

class PaymentController extends Controller
{
    public function paymentRegistration(Request $request)
    {


        $user_data = session()->get('user_data');  // Get user data from the session
        $name = $user_data['name'];
        $email = $user_data['email'];
        $phone_number = $user_data['phone_number'];
        $fixedAmount = 100; // Fixed amount for registration

        // Calculate the payment amount (convert to cents for Toyyibpay API)
        $amount = $fixedAmount * 100; // Convert to cents
        $code = config('toyyibpay.category_codes.yuran_khairat'); // Get category code from config

        // Store the amount in session for later use (in case you need to retrieve it later)
        session(['Yuran_pendaftaran' => $amount]);

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
        $data = Toyyibpay::createBill($code, (object)$billData);

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

    public function handlePaymentCallback(Request $request)
    {
        $status = $request->input('status_id'); // 1 = success, 0 = failure
        $billCode = $request->input('billcode'); // ToyyibPay bill code
        $amount = session('infaq_amount', 0); // Amount from the session (in cents)

        // Get the user's data from session
        $user_data = session()->get('user_data');
        $dependent_data = session()->get('dependents');

        // Log the payment information for debugging
        \Log::info('Payment Status:', ['status' => $status, 'billCode' => $billCode, 'amount' => $amount]);

        if ($status == 1) {  // If payment is successful
            // Check if the user already exists

            $user = User::Create([
                    'No_Ahli' => $user_data['No_Ahli'],
                    'ic_number' => $user_data['ic_number'],
                    'name' => $user_data['name'],
                    'email' => $user_data['email'],
                    'password' => $user_data['password'] ,
                    'phone_number' => $user_data['phone_number'],
                    'address' => $user_data['address'],
                    'age' => $user_data['age'],
                    'home_phone' => $user_data['home_phone'],
                    'Residency_Stat' => $user_data['residence_status'],

            ]);





            // Store the dependent data in the database
            foreach ($dependent_data as $dependent) {
                Dependent::create([
                    'No_Ahli' => $dependent['No_Ahli'],
                    'full_name' => $dependent['dependent_full_name'],
                    'relationship' => $dependent['dependent_relationship'],
                    'age' => $dependent['dependent_age'],
                    'ic_number' => $dependent['dependent_ic_number'],
                ]);
            }

            // Update the Khairat Kematian Funds table with payment status
            $fund = KhairatKematianFund::where('bill_code', $billCode)->first();  // Find the payment record by bill code

            if ($fund) {
                // Update the payment status to 'paid' and set the payment date
                $fund->payment_status = 'paid';
                $fund->payment_date = now();  // Set the payment date to the current date/time
                $fund->save();  // Save the changes
            }

            // Log in the user
            Auth::login($user);

            // Redirect the user to the dashboard after successful payment and registration
            return redirect()->route('dashboard')->with('message', 'Payment Successful and User Registered!');
        } else {
            // Payment failed
            return redirect()->route('payment.failed')->with('error', 'Payment failed! Please try again.');
        }
    }

}
