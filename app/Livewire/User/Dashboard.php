<?php

namespace App\Livewire\User;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\PaymentCategory;

class Dashboard extends Component
{
    public $state = [];
    public $totalPayments;
    public $outstandingAmount;
    public $outstandingCategories;
    public $userPayments;

    public function mount()
    {
        $this->state = Auth::user()->toArray();
        $user = auth()->user();

        // Get all active payment categories
        $requiredPaymentCategories = PaymentCategory::where('category_status', true)->get();

        // Get user's completed payments
        $this->userPayments = $user->payments()
            ->where('status_id', 1) // Only count completed payments
            ->get();

        // Calculate total payments made
        $this->totalPayments = $this->userPayments->sum('amount');

        // Calculate required payments (all active categories)
        $requiredPaymentTotal = $requiredPaymentCategories->sum('amount');

        // Calculate outstanding amount
        $this->outstandingAmount = max(0, $requiredPaymentTotal - $this->totalPayments);

        // Get individual outstanding payment categories
        $paidCategoryIds = $this->userPayments->pluck('payment_category_id')->unique()->toArray();
        $this->outstandingCategories = $requiredPaymentCategories->whereNotIn('id', $paidCategoryIds);
    }

    public function render()
    {
        return view('livewire.user.dashboard', [
            'payments' => $this->userPayments,
            'totalPayments' => $this->totalPayments,
            'outstandingAmount' => $this->outstandingAmount,
            'outstandingCategories' => $this->outstandingCategories
        ]);
    }

    // In your Dashboard Livewire component
public function scrollToOutstanding()
{
    $this->dispatchBrowserEvent('scroll-to-outstanding');
}
}
