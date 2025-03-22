<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Http\Request;
use App\Models\Dependent;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;


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

            $userRole = Role::where('name', 'user')->first();
            if ($userRole) {
                $user->roles()->attach($userRole->id);
            }

            // Store the dependents
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
                'request_title' => $request->input('category_name'),
                'paid_at' => now(), // Set the current date and time
            ]);

            // Clear the session data
            session()->forget('user_data');
            session()->forget('dependents');

            // Log the newly created user in
            Auth::login($user); // Use the $user object directly
            session()->regenerate(); // Regenerate the session to avoid conflicts

            // Redirect to the dashboard after successful registration
            return redirect()->route('dashboard');
        } else {
            // Payment failed
            return redirect()->route('home')->with('error', 'Payment failed! Please try again.');
        }
    }
}
