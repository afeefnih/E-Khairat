<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Dependent;
use Illuminate\Support\Facades\Auth;


class registrationController extends Controller
{
    public function storedUser($user_data){

        $existingUser = User::where('No_Ahli', $user_data['No_Ahli'])->first();
        if ($existingUser) {
            // Generate the next No_Ahli value
            $lastUser = User::orderBy('No_Ahli', 'desc')->first();
            $nextNoAhli = $lastUser ? str_pad((int)$lastUser->No_Ahli + 1, 4, '0', STR_PAD_LEFT) : '0001';
            $user_data['No_Ahli'] = $nextNoAhli;
        }


        $user = User::Create([
            'No_Ahli' => $user_data['No_Ahli'],
            'ic_number' => $user_data['ic_number'],
            'name' => $user_data['name'],
            'email' => $user_data['email'],
            'password' => $user_data['password'],
            'phone_number' => $user_data['phone_number'],
            'address' => $user_data['address'],
            'age' => $user_data['age'],
            'home_phone' => $user_data['home_phone'],
            'residence_status' => $user_data['residence_status'],
        ]);



        return $user;

    }

    public function storedDependent($dependent_data){

        foreach ($dependent_data as $dependent) {
            Dependent::create([
                'No_Ahli' => $dependent['No_Ahli'],
                'full_name' => $dependent['full_name'],
                'relationship' => $dependent['relationship'],
                'age' => $dependent['age'],
                'ic_number' => $dependent['ic_number'],
            ]);
        }

    }

    public function handlePaymentCallback(Request $request)
    {
        $status = $request->input('status_id'); // Check payment status
        $billCode = $request->input('billcode'); // Get ToyyibPay bill code
        $amount = session('infaq_amount', 0); // Amount from session

        $user_data = session()->get('user_data');
        $dependent_data = session()->get('dependents');

        if ($status == 1) {
            // If payment is successful
            // Get the user data from session
            $user = $this->storedUser($user_data);
            $this->storedDependent($dependent_data);

            session()->forget('user_data');
            session()->forget('dependents');


            Auth::login($user);
            // Redirect to the dependent registration step after successfully storing user
            return redirect()->intended(route('dashboard'))->with('message', 'Selamat datang! Pendaftaran berjaya.');
        } else {
            return redirect()->route('payment.failed')->with('error', 'Payment failed! Please try again.');
        }
    }
}
