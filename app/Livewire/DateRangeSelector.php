<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Payment;

class DateRangeSelector extends Component
{
    public $startDate;
    public $endDate;
    public $preset = 'thisYear'; // Default to allTime
    public $presets = [
        'today' => 'Hari Ini',
        'yesterday' => 'Semalam',
        'last7days' => '7 Hari Lepas',
        'last30days' => '30 Hari Lepas',
        'thisMonth' => 'Bulan Ini',
        'lastMonth' => 'Bulan Lepas',
        'thisYear' => 'Tahun Ini',
        'custom' => 'Tempoh Kustom',
    ];

    public function mount()
    {
        // Set default date range to all time
        $savedStartDate = Session::get('dashboard_start_date');
        $savedEndDate = Session::get('dashboard_end_date');
        $savedPreset = Session::get('dashboard_date_preset');

        if ($savedStartDate && $savedEndDate) {
            $this->startDate = $savedStartDate;
            $this->endDate = $savedEndDate;
            $this->preset = $savedPreset ?? 'last30days'; // Default to last30days
        } else {
            $this->setPresetDates('last30days'); // Default to last30days
        }
    }

    public function setPresetDates($preset)
    {
        $this->preset = $preset;

        switch ($preset) {
            case 'allTime':
                // Get earliest record date and latest record date
                $this->startDate = $this->getEarliestRecordDate()->format('Y-m-d');
                $this->endDate = $this->getLatestRecordDate()->format('Y-m-d');
                break;
            case 'today':
                $this->startDate = Carbon::today()->format('Y-m-d');
                $this->endDate = Carbon::today()->format('Y-m-d');
                break;
            case 'yesterday':
                $this->startDate = Carbon::yesterday()->format('Y-m-d');
                $this->endDate = Carbon::yesterday()->format('Y-m-d');
                break;
            case 'last7days':
                $this->startDate = Carbon::today()->subDays(6)->format('Y-m-d');
                $this->endDate = Carbon::today()->format('Y-m-d');
                break;
            case 'last30days':
                $this->startDate = Carbon::today()->subDays(29)->format('Y-m-d');
                $this->endDate = Carbon::today()->format('Y-m-d');
                break;
            case 'thisMonth':
                $this->startDate = Carbon::today()->startOfMonth()->format('Y-m-d');
                $this->endDate = Carbon::today()->endOfMonth()->format('Y-m-d');
                break;
            case 'lastMonth':
                $this->startDate = Carbon::today()->subMonth()->startOfMonth()->format('Y-m-d');
                $this->endDate = Carbon::today()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            case 'thisYear':
                $this->startDate = Carbon::today()->startOfYear()->format('Y-m-d');
                $this->endDate = Carbon::today()->endOfYear()->format('Y-m-d');
                break;
            case 'custom':
                // Don't change dates for custom selection
                break;
        }

        $this->updateDateRange();
    }

    private function getEarliestRecordDate()
    {
        try {
            // Find the earliest records from multiple tables
            $earliestUser = User::orderBy('created_at', 'asc')->first();
            $earliestTransaction = Transaction::orderBy('transaction_date', 'asc')->first();
            $earliestPayment = Payment::orderBy('created_at', 'asc')->first();

            // Collect all dates
            $dates = [];
            if ($earliestUser) $dates[] = $earliestUser->created_at;
            if ($earliestTransaction) $dates[] = $earliestTransaction->transaction_date;
            if ($earliestPayment) $dates[] = $earliestPayment->created_at;

            // Return the earliest date or fallback
            if (!empty($dates)) {
                $minDate = min($dates);
                return Carbon::parse($minDate);
            }

            // Fallback: if no records, return 5 years ago for a substantial "all time" view
            return Carbon::today()->subYears(5);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error finding earliest record date: ' . $e->getMessage());

            // If any error occurs, return a safe default
            return Carbon::today()->subYears(5);
        }
    }

    private function getLatestRecordDate()
    {
        try {
            // Find the latest records from multiple tables
            $latestUser = User::orderBy('created_at', 'desc')->first();
            $latestTransaction = Transaction::orderBy('transaction_date', 'desc')->first();
            $latestPayment = Payment::orderBy('created_at', 'desc')->first();

            // Collect all dates
            $dates = [];
            if ($latestUser) $dates[] = $latestUser->created_at;
            if ($latestTransaction) $dates[] = $latestTransaction->transaction_date;
            if ($latestPayment) $dates[] = $latestPayment->created_at;

            // Find latest date from records
            if (!empty($dates)) {
                $maxDate = max($dates);
                $latestDate = Carbon::parse($maxDate);

                // If the latest record date is in the future, use it
                // Otherwise, add 1 year to today to include potential future records
                if ($latestDate->isAfter(Carbon::today())) {
                    return $latestDate;
                }
            }

            // If no future dates or no records, set to 1 year from now
            return Carbon::today()->addYear();

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error finding latest record date: ' . $e->getMessage());

            // If any error occurs, return a safe default (1 year from now)
            return Carbon::today()->addYear();
        }
    }

    public function updatedStartDate()
    {
        // If manually updating date, switch to custom preset
        $this->preset = 'custom';
        $this->updateDateRange();
    }

    public function updatedEndDate()
    {
        // If manually updating date, switch to custom preset
        $this->preset = 'custom';
        $this->updateDateRange();
    }

    public function updateDateRange()
    {
        // Store dates in session
        Session::put('dashboard_start_date', $this->startDate);
        Session::put('dashboard_end_date', $this->endDate);
        Session::put('dashboard_date_preset', $this->preset);

        // Add logging for debugging
        \Log::info("Date range updated: {$this->startDate} to {$this->endDate} (preset: {$this->preset})");

        // Emit event for other components to listen for - include preset in data
        $this->dispatch('dateRangeChanged', [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'preset' => $this->preset  // Add preset to dispatched data
        ]);
    }

    public function render()
    {
        return view('livewire.date-range-selector');
    }
}
