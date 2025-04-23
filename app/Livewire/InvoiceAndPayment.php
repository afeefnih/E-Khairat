<?php
// app/Http/Livewire/InvoiceAndPayment.php
namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http; // Import Http facade
use App\Models\PaymentCategory;
use App\Http\Livewire\DependentRegistration;

class InvoiceAndPayment extends Component
{
    public $user_data;
    public $dependents;
    public $amount ;

    public function mount()
    {
        // Get user data from the session
        $this->user_data = session()->get('user_data', []);
        // Get dependent data from the session (if any)
        $this->dependents = session()->get('dependents', []);

        $category = PaymentCategory::find(1);

        if ($category) {
            $this->amount = $category->amount;
        } else {
            $this->amount = 0; // Default if not found
        }
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


    public function backToRegister()
    {
        $this->redirectRoute('register', navigate: true);
    }

    public function render()
    {
        return view('livewire.invoice-and-payment');
    }
}
