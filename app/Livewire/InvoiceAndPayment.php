<?php
// app/Http/Livewire/InvoiceAndPayment.php
namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http; // Import Http facade

class InvoiceAndPayment extends Component
{
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

    public function paymentRegistration()
    {
        // Send the request with CSRF token and other data
        $response = Http::withHeaders([
            'X-CSRF-TOKEN' => csrf_token(), // Include CSRF token
        ])->post(route('payment.registration'), [
            'amount' => $this->amount,
            'No_Ahli' => $this->user_data['No_Ahli'],
        ]);

        // You can handle the response from the controller if needed
        if ($response->successful()) {
            // Handle success (e.g. show success message)
        } else {
            dd($response->body()); // Debug the response
        }
    }

    public function addDependent()
    {
        $this->redirect(DependentRegistration::class, navigate: true);
    }

    public function backToRegister()
    {
        $this->redirectRoute('register', navigate: true);
    }

    public function render()
    {
        return view('livewire.invoice-and-payment');
    }
}
