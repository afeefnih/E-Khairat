<?php

namespace App\Livewire\User;

use App\Models\Payment;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\PaymentCategory;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Jobs\GeneratePaymentReceipt;
use Illuminate\Support\Facades\Storage;

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

    // Add the download receipt function
    public function downloadReceipt($paymentId)
    {
        // Get payment with related data
        $payment = Payment::findOrFail($paymentId);

        // Check if user is authorized to view this receipt
        if (auth()->id() !== $payment->user_id && !auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        // Check if receipt already exists
        $receiptNumber = 'INV-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT);
        $filename = "receipts/" . auth()->id() . "/{$receiptNumber}.pdf";

        if (Storage::disk('public')->exists($filename)) {
            return Storage::disk('public')->download($filename);
        }

        // Dispatch job to generate PDF
        GeneratePaymentReceipt::dispatch($paymentId, auth()->id());

        // Notify user that PDF is being generated
        $this->dispatch('notify', [
            'type' => 'info',
            'message' => 'Your receipt is being generated. You will be notified when it\'s ready.'
        ]);
    }

    public function downloadGeneratedReceipt($filename)
    {
        if (Storage::disk('public')->exists($filename)) {
            return Storage::disk('public')->download($filename);
        }

        $this->dispatch('notify', [
            'type' => 'error',
            'message' => 'Receipt file not found. Please try generating it again.'
        ]);
    }
}
