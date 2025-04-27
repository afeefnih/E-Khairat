<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Toyyibpay;
use App\Models\User;
use App\Models\Dependent;
use App\Models\Payment;
use App\Models\PaymentCategory;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use App\Models\KhairatKematianFund;
use App\Livewire\UserRegistration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Notifications\NewMemberRegistration;

class PaymentController extends Controller
{
    /**
     * Handle payment for user registration
     */

     protected function getAdmins()
{
    // Using your role system to find admins
    return \App\Models\User::whereHas('roles', function($query) {
        $query->where('name', 'admin');
    })->get();
}


    public function paymentRegistration(Request $request)
    {
        $user_data = session()->get('user_data'); // Get user data from the session
        $dependent_data = session()->get('dependents');
        $name = $user_data['name'];
        $email = $user_data['email'];
        $phone_number = $user_data['phone_number'];

        // Get the payment category for registration
        $paymentCategory = PaymentCategory::where('category_name', 'Bayaran Pendaftran')->where('category_status', true)->first();

        $amount = ($paymentCategory ? $paymentCategory->amount : 100) * 100; // Convert to cents

        // Store payment data in session for callback
        session()->put('payment_data', [
            'payment_category_id' => $paymentCategory ? $paymentCategory->id : null,
            'amount' => $amount / 100, // Store in RM, not cents
            'category_name' => $paymentCategory ? $paymentCategory->category_name : 'Pendaftaran Khairat Kematian',
            'is_registration' => true, // Flag to identify registration payments
        ]);

        $code = config('toyyibpay.category_codes.yuran_khairat'); // Get category code from config

        // Prepare the data for the Toyyibpay bill
        $billData = [
            'billName' => $paymentCategory ? $paymentCategory->category_name : 'Pendaftaran Khairat Kematian',
            'billDescription' => 'Payment for ' . ($paymentCategory ? $paymentCategory->category_name : 'Registration'),
            'billPriceSetting' => 1, // Fixed amount
            'billPayorInfo' => 1, // Set to 1 to allow user to enter their info
            'billAmount' => $amount,
            'billReturnUrl' => route('payment.callback'), // URL to redirect after payment
            'billExternalReferenceNo' => uniqid(), // Unique reference number
            'billTo' => $name, // User's name
            'billEmail' => $email ?? 'no-email@example.com', // Use default if email is not provided
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
            return redirect()->route('home')->with('error', 'Failed to create payment bill. Please try again.');
        }
    }

    /**
     * Show outstanding payments page
     */
    public function showOutstanding()
    {
        $user = auth()->user();

        // Get all active payment categories
        $requiredPaymentCategories = PaymentCategory::where('category_status', true)->get();

        // Get user's completed payments
        $paidCategoryIds = $user->payments()->where('status_id', 1)->pluck('payment_category_id')->unique()->toArray();

        // Get outstanding categories
        $outstandingCategories = $requiredPaymentCategories->whereNotIn('id', $paidCategoryIds);

        return view('livewire.payments.outstanding') // Use the Livewire component
            ->with('outstandingCategories', $outstandingCategories);
    }

    /**
     * Process payment for a specific category
     */
    public function processPayment(PaymentCategory $category)
    {
        $user = auth()->user();

        // Check if payment has already been made
        $existingPayment = $user->payments()->where('payment_category_id', $category->id)->where('status_id', 1)->first();

        if ($existingPayment) {
            return redirect()->route('dashboard')->with('error', 'You have already paid for this category.');
        }

        // Store payment data in session
        session()->put('payment_data', [
            'user_id' => $user->id,
            'payment_category_id' => $category->id,
            'amount' => $category->amount,
            'category_name' => $category->category_name,
            'is_registration' => false, // This is not a registration payment
        ]);

        // Set up payment data for Toyyibpay
        $amount = $category->amount * 100; // Convert to cents
        $name = $user->name;
        $email = $user->email;
        $phone_number = $user->phone_number;

        $code = config('toyyibpay.category_codes.yuran_khairat'); // Get category code from config

        // Prepare the data for the Toyyibpay bill
        $billData = [
            'billName' => $category->category_name,
            'billDescription' => 'Payment for ' . $category->category_name,
            'billPriceSetting' => 1, // Fixed amount
            'billPayorInfo' => 1, // Set to 1 to allow user to enter their info
            'billAmount' => $amount,
            'billReturnUrl' => route('payment.callback'), // URL to redirect after payment
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
            return redirect()->route('dashboard')->with('error', 'Failed to create payment bill. Please try again.');
        }
    }

    /**
     * Handle payment callback from Toyyibpay
     */
    public function paymentCallback(Request $request)
    {
        $status = $request->input('status_id');
        $billcode = $request->input('billcode');
        $orderId = $request->input('order_id');

        // Get payment data from session
        $paymentData = session()->get('payment_data');
        $isRegistration = $paymentData['is_registration'] ?? false;

        if ($isRegistration) {
            // This is a registration payment
            return $this->handleRegistrationPayment($request, $status, $billcode, $orderId, $paymentData);
        } else {
            // This is a regular payment for an existing user
            return $this->handleRegularPayment($request, $status, $billcode, $orderId, $paymentData);
        }
    }

    /**
     * Handle payment for new user registration
     */
    private function handleRegistrationPayment($request, $status, $billcode, $orderId, $paymentData)
    {
        // Get user data and dependent data from session
        $user_data = session()->get('user_data');
        $dependent_data = session()->get('dependents');

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

            // Assign the 'user' role to the newly created user
            $userRole = Role::where('name', 'user')->first();
            if ($userRole) {
                $user->roles()->attach($userRole->id);
            }

            // Notify admins about new registration
            $admins = $this->getAdmins();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\NewMemberRegistration($user));
            }

            // Store the dependents
            if (is_array($dependent_data)) {
                foreach ($dependent_data as $dependent) {
                    Dependent::create([
                        'user_id' => $user->id, // Associate with the newly created user
                        'full_name' => $dependent['full_name'],
                        'relationship' => $dependent['relationship'],
                        'age' => $dependent['age'],
                        'ic_number' => $dependent['ic_number'],
                    ]);
                }
            }

            // Create the new payment
            Payment::create([
                'user_id' => $user->id,
                'payment_category_id' => $paymentData['payment_category_id'] ?? 1,
                'amount' => $paymentData['amount'] ?? 100,
                'status_id' => 1, // Set the payment status
                'billcode' => $billcode,
                'order_id' => $orderId,
                'request_title' => $paymentData['category_name'] ?? 'Registration Payment',
                'paid_at' => now(), // Set the current date and time
            ]);

            // Clear the session data
            session()->forget('user_data');
            session()->forget('dependents');
            session()->forget('payment_data');

            // Log the newly created user in
            Auth::login($user); // Use the $user object directly
            session()->regenerate(); // Regenerate the session to avoid conflicts

            // Redirect to the dashboard after successful registration
            return redirect()->route('dashboard')->with('success', 'Registration completed successfully! Welcome to our platform.');
        } else {
            // Payment failed
            return redirect()->route('home')->with('error', 'Payment failed! Please try again.');
        }
    }

    /**
     * Handle payment for existing user
     */
    private function handleRegularPayment($request, $status, $billcode, $orderId, $paymentData)
    {
        $userId = $paymentData['user_id'] ?? auth()->id();
        $categoryId = $paymentData['payment_category_id'];

        if ($status == 1) {
            // Payment successful
            // Find existing payment record for this user and category
            $payment = Payment::where('user_id', $userId)->where('payment_category_id', $categoryId)->first();

            if ($payment) {
                // Update existing payment
                $payment->update([
                    'status_id' => 1, // Mark as paid
                    'billcode' => $billcode,
                    'order_id' => $orderId,
                    'paid_at' => now(),
                    'amount' => $paymentData['amount'], // Update amount if needed
                ]);

                session()->forget('payment_data');

                return redirect()->route('dashboard')->with('success', 'Payment completed successfully!');
            } else {
                return redirect()->route('dashboard')->with('error', 'Payment failed. Please try again.');
            }
        }
    }
}
