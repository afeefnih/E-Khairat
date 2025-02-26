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



        return $user;

    }

    public function storedDependent($dependent_data, $user_id)
{
    foreach ($dependent_data as $dependent) {
        Dependent::create([
            'user_id' => $user_id, // Associate with the newly created user
            'full_name' => $dependent['full_name'],
            'relationship' => $dependent['relationship'],
            'age' => $dependent['age'],
            'ic_number' => $dependent['ic_number'],
        ]);
    }
}


public function storePayment(Request $request)
{
    // Validate the incoming request data
    $request->validate([
        'user_id' => 'required|exists:users,id', // Ensure user_id exists in users table
        'payment_category_id' => 'required|exists:payment_categories,id', // Ensure payment_category_id exists in payment_categories table
        'amount' => 'required|numeric', // The amount should be numeric
        'status_id' => 'required|in:1,2,3', // Ensure status_id is one of: 1 = success, 2 = pending, 3 = failed
        'billcode' => 'required|string|unique:payments', // billcode must be unique in the payments table
        'order_id' => 'nullable|string', // Order ID is optional
        'request_title' => 'nullable|string', // Request title is optional
        'paid_at' => 'nullable|date', // Paid date is optional
    ]);

    // Create the new payment
    $payment = Payment::create([
        'user_id' => $request->user_id, // Reference to the user making the payment
        'payment_category_id' => $request->payment_category_id, // Reference to the payment category
        'amount' => $request->amount,
        'status_id' => $request->status_id, // e.g., 1 = success, 2 = pending, 3 = failed
        'billcode' => $request->billcode, // Unique billcode
        'order_id' => $request->order_id, // External order reference (optional)
        'request_title' => $request->request_title, // Request title (optional)
        'paid_at' => $request->paid_at, // Paid timestamp (optional)
    ]);

    return $payment; // Return the payment object after it is created
}


public function handlePaymentCallback(Request $request)
{
    $status = $request->input('status_id'); // Check payment status

    // Get user data and dependent data from session
    $user_data = session()->get('user_data');
    $dependent_data = session()->get('dependents');

    if ($status == 1) {
        // If payment is successful
        try {
            // Get the user data from session
            $user = $this->storedUser($user_data);

            // Store the dependents with the user_id
            $this->storedDependent($dependent_data, $user->id);

            // Store the payment
            $this->storePayment($request);

            // Clear the session data
            session()->forget('user_data');
            session()->forget('dependents');

            // Log the user in
            Auth::login($user);
            session()->regenerate(); // Regenerate the session to avoid conflicts

            // Redirect to the dashboard after successful registration
            return redirect()->intended(route('dashboard'))->with('message', 'Selamat datang! Pendaftaran berjaya.');
        } catch (\Exception $e) {
            // Handle any exceptions that occur during the process
            return redirect()->route('home')->with('error', 'An error occurred during the registration process. Please try again.');
        }
    } else {
        // Payment failed
        return redirect()->route('payment.failed')->with('error', 'Payment failed! Please try again.');
    }
}

}
