<?php

namespace App\Livewire\User;

use App\Models\Payment;
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

    // Get payment categories that are BOTH active AND have payments for this user
    $eligibleCategories = PaymentCategory::where('category_status', true)
        ->whereHas('payments', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->get();

    // Get user's completed payments (status = 1)
    $this->userPayments = $user->payments()
        ->where('status_id', 1)
        ->get();

    // Calculate total payments made
    $this->totalPayments = $this->userPayments->sum('amount');

    // Calculate required payments total (sum of all eligible categories)
    $requiredPaymentTotal = $eligibleCategories->sum('amount');

    // Calculate outstanding amount
    $this->outstandingAmount = max(0, $requiredPaymentTotal - $this->totalPayments);

    // Get paid category IDs
    $paidCategoryIds = $this->userPayments->pluck('payment_category_id')
        ->unique()
        ->toArray();

    // Outstanding categories are eligible but not paid
    $this->outstandingCategories = $eligibleCategories->whereNotIn('id', $paidCategoryIds);
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
