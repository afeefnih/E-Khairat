<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class DateRangeSelector extends Component
{
    public $startDate;
    public $endDate;
    public $preset = 'last30days';
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
        // Set default date range to last 30 days
        $savedStartDate = Session::get('dashboard_start_date');
        $savedEndDate = Session::get('dashboard_end_date');
        $savedPreset = Session::get('dashboard_date_preset');

        if ($savedStartDate && $savedEndDate) {
            $this->startDate = $savedStartDate;
            $this->endDate = $savedEndDate;
            $this->preset = $savedPreset ?? 'custom';
        } else {
            $this->setPresetDates('last30days');
        }
    }

    public function setPresetDates($preset)
    {
        $this->preset = $preset;

        switch ($preset) {
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

        // Emit event for other components to listen for
        $this->dispatch('dateRangeChanged', [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate
        ]);
    }

    public function render()
    {
        return view('livewire.date-range-selector');
    }
}
