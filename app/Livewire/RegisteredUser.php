<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Http\Request;
use App\Models\Dependent;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RegisteredUser extends Component
{
    public function render()
    {
        return null;
    }

    public function handlePaymentCallback(Request $request)
    {
        $status = $request->input('status_id'); // Check payment status

        // Get user data and dependent data from session

        $user_data = session()->get('user_data');
        $dependent_data = session()->get('dependents');
        $payment_data = session()->get('payment_data');

        $user = User::where('No_Ahli', $user_data['No_Ahli'])->first();

        if ($status == 1) {
            // If payment is successful
            $user = User::create([
                'No_Ahli' => $user_data['No_Ahli'],
                'ic_number' => $user_data['ic_number'],
                'name' => $user_data['name'],
                'email' => $user_data['email'],
                'password' => bcrypt($user_data['password']), // Hash the password before saving
                'phone_number' => $user_data['phone_number'],
                'address' => $user_data['address'],
                'age' => $user_data['age'],
                'home_phone' => $user_data['home_phone'],
                'residence_status' => $user_data['residence_status'],
            ]);

            // Store the payment
            foreach ($dependent_data as $dependent) {
                Dependent::create([
                    'user_id' => $user->id, // Associate with the newly created user
                    'full_name' => $dependent['full_name'],
                    'relationship' => $dependent['relationship'],
                    'age' => $dependent['age'],
                    'ic_number' => $dependent['ic_number'],
                ]);
            }

            // Create the new payment
            $payment = Payment::create([
                'user_id' => $user->id,
                'payment_category_id' => 1, // Set the payment category ID
                'amount' => 100, // Set the payment amount
                'status_id' => 1, // Set the payment status
                'billcode' => $request->input('billcode'),
                'order_id' => $request->input('order_id'),
                'request_title' => $request->input('request_title'),
                'paid_at' => now(), // Set the current date and time
            ]);

            // Store the dependents

            // Clear the session data
            session()->forget('user_data');
            session()->forget('dependents');

            // Log the user in
            Auth::login(User::find($user_data['No_Ahli']));
            session()->regenerate(); // Regenerate the session to avoid conflicts

            // Redirect to the dashboard after successful registration
            return redirect()->intended(route('dashboard'))->with('message', 'Selamat datang! Pendaftaran berjaya.');
        } else {
            // Payment failed
            return redirect()->route('payment.failed')->with('error', 'Payment failed! Please try again.');
        }
    }
}
