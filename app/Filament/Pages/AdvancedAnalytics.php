<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\Payment;
use App\Models\DeathRecord;
use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AdvancedAnalytics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.pages.advanced-analytics';
    protected static ?string $navigationLabel = 'Analisis Lanjutan';
    protected static ?string $title = 'Analisis Lanjutan';
    protected static ?int $navigationSort = 3;

    public $period = 'year';
    public $memberGrowthData = [];
    public $paymentTrendsData = [];
    public $deathRecordsData = [];
    public $ageDistributionData = [];
    public $isLoading = false; // For loading state

    // Add summary statistics
    public $totalNewMembers = 0;
    public $totalPayments = 0;
    public $totalDeaths = 0;

    public function mount()
    {
        // Check for period parameter in URL
        if (request()->has('period')) {
            $this->period = request()->get('period');
        }

        $this->loadAllData();
    }

    public function setPeriod($period)
    {
        \Log::info('Period changed to: ' . $period);
        $this->period = $period;
        $this->isLoading = true; // Set loading state
        $this->loadAllData();
        $this->isLoading = false; // Reset loading state
        $this->dispatch('forceRefresh');
    }

    private function loadAllData()
    {
        // Use cache to improve performance - cache for 1 hour
        $cacheKey = "analytics_{$this->period}_" . auth()->id();
        $cacheExpiry = 60; // 1 hour in minutes

        if (Cache::has($cacheKey)) {
            $data = Cache::get($cacheKey);
            $this->memberGrowthData = $data['memberGrowthData'];
            $this->paymentTrendsData = $data['paymentTrendsData'];
            $this->deathRecordsData = $data['deathRecordsData'];
            $this->ageDistributionData = $data['ageDistributionData'];
            $this->totalNewMembers = $data['totalNewMembers'];
            $this->totalPayments = $data['totalPayments'];
            $this->totalDeaths = $data['totalDeaths'];
        } else {
            $this->loadMemberGrowthData();
            $this->loadPaymentTrendsData();
            $this->loadDeathRecordsData();
            $this->loadAgeDistributionData();

            // Calculate summary metrics
            $this->calculateSummaryMetrics();

            // Cache the results
            Cache::put($cacheKey, [
                'memberGrowthData' => $this->memberGrowthData,
                'paymentTrendsData' => $this->paymentTrendsData,
                'deathRecordsData' => $this->deathRecordsData,
                'ageDistributionData' => $this->ageDistributionData,
                'totalNewMembers' => $this->totalNewMembers,
                'totalPayments' => $this->totalPayments,
                'totalDeaths' => $this->totalDeaths,
            ], $cacheExpiry);
        }
    }

    private function calculateSummaryMetrics()
    {
        // Calculate total new members in period
        $this->totalNewMembers = array_sum(array_column($this->memberGrowthData, 'value'));

        // Calculate total payments in period
        $this->totalPayments = array_sum(array_column($this->paymentTrendsData, 'value'));

        // Calculate total deaths in period
        $this->totalDeaths = array_sum($this->deathRecordsData['memberDeaths']) +
                            array_sum($this->deathRecordsData['dependentDeaths']);
    }

    private function loadMemberGrowthData()
{
    $period = $this->period;
    $data = [];

    // Optimize query by using query builder more efficiently
    $userQuery = User::whereHas('roles', function ($query) {
        $query->where('name', 'user');
    });

    if ($period == 'year') {
        // Get monthly member growth over the last year
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $startOfMonth = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();
            $label = $month->format('M Y');

            $count = (clone $userQuery)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();

            $data[] = [
                'label' => $label,
                'value' => $count
            ];
        }
    } elseif ($period == 'quarter') {
        // Weekly data for the last 3 months
        for ($i = 11; $i >= 0; $i--) {
            $startDate = Carbon::now()->startOfWeek()->subWeeks($i);
            $endDate = Carbon::now()->startOfWeek()->subWeeks($i)->endOfWeek();
            $label = $startDate->format('d M') . ' - ' . $endDate->format('d M');

            $count = (clone $userQuery)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $data[] = [
                'label' => $label,
                'value' => $count
            ];
        }
    } else { // month
        // Daily data for the last 30 days - using a more straightforward approach
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $label = $date->format('d M');

            $count = (clone $userQuery)
                ->whereDate('created_at', $date)
                ->count();

            $data[] = [
                'label' => $label,
                'value' => $count
            ];
        }
    }

    $this->memberGrowthData = $data;
}
    private function loadPaymentTrendsData()
    {
        $period = $this->period;
        $data = [];

        if ($period == 'year') {
            // Monthly payment trends for the last year
            for ($i = 11; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $label = $month->format('M Y');

                $amount = Payment::where('status_id', '1')
                    ->whereMonth('paid_at', $month->month)
                    ->whereYear('paid_at', $month->year)
                    ->sum('amount');

                $data[] = [
                    'label' => $label,
                    'value' => $amount
                ];
            }
        } elseif ($period == 'quarter') {
            // Weekly payment trends for the last 3 months
            for ($i = 11; $i >= 0; $i--) {
                $startDate = Carbon::now()->startOfWeek()->subWeeks($i);
                $endDate = Carbon::now()->startOfWeek()->subWeeks($i)->endOfWeek();
                $label = $startDate->format('d M') . ' - ' . $endDate->format('d M');

                $amount = Payment::where('status_id', '1')
                    ->whereBetween('paid_at', [$startDate, $endDate])
                    ->sum('amount');

                $data[] = [
                    'label' => $label,
                    'value' => $amount
                ];
            }
        } else { // month
            // Daily payment trends for the last 30 days
            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $label = $date->format('d M');

                $amount = Payment::where('status_id', '1')
                    ->whereDate('paid_at', $date)
                    ->sum('amount');

                $data[] = [
                    'label' => $label,
                    'value' => $amount
                ];
            }
        }

        $this->paymentTrendsData = $data;
    }

    private function loadDeathRecordsData()
    {
        $period = $this->period;
        $memberDeaths = [];
        $dependentDeaths = [];
        $labels = [];

        if ($period == 'year') {
            // Monthly death records for the last year
            for ($i = 11; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $labels[] = $month->format('M Y');

                $memberCount = DeathRecord::where('deceased_type', 'App\\Models\\User')
                    ->whereMonth('date_of_death', $month->month)
                    ->whereYear('date_of_death', $month->year)
                    ->count();

                $dependentCount = DeathRecord::where('deceased_type', 'App\\Models\\Dependent')
                    ->whereMonth('date_of_death', $month->month)
                    ->whereYear('date_of_death', $month->year)
                    ->count();

                $memberDeaths[] = $memberCount;
                $dependentDeaths[] = $dependentCount;
            }
        } elseif ($period == 'quarter') {
            // Weekly death records for the last 3 months
            for ($i = 11; $i >= 0; $i--) {
                $startDate = Carbon::now()->startOfWeek()->subWeeks($i);
                $endDate = Carbon::now()->startOfWeek()->subWeeks($i)->endOfWeek();
                $labels[] = $startDate->format('d M') . ' - ' . $endDate->format('d M');

                $memberCount = DeathRecord::where('deceased_type', 'App\\Models\\User')
                    ->whereBetween('date_of_death', [$startDate, $endDate])
                    ->count();

                $dependentCount = DeathRecord::where('deceased_type', 'App\\Models\\Dependent')
                    ->whereBetween('date_of_death', [$startDate, $endDate])
                    ->count();

                $memberDeaths[] = $memberCount;
                $dependentDeaths[] = $dependentCount;
            }
        } else { // month
            // Last 30 days death records
            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $labels[] = $date->format('d M');

                $memberCount = DeathRecord::where('deceased_type', 'App\\Models\\User')
                    ->whereDate('date_of_death', $date)
                    ->count();

                $dependentCount = DeathRecord::where('deceased_type', 'App\\Models\\Dependent')
                    ->whereDate('date_of_death', $date)
                    ->count();

                $memberDeaths[] = $memberCount;
                $dependentDeaths[] = $dependentCount;
            }
        }

        $this->deathRecordsData = [
            'labels' => $labels,
            'memberDeaths' => $memberDeaths,
            'dependentDeaths' => $dependentDeaths
        ];
    }

    private function loadAgeDistributionData()
    {
        // Age distribution for members
        $ageRanges = [
            '18-30' => [18, 30],
            '31-40' => [31, 40],
            '41-50' => [41, 50],
            '51-60' => [51, 60],
            '61+' => [61, 999]
        ];

        $distribution = [];

        foreach ($ageRanges as $range => $limits) {
            $count = User::whereHas('roles', function ($query) {
                $query->where('name', 'user');
            })
            ->whereDoesntHave('deathRecord')
            ->where('age', '>=', $limits[0])
            ->where('age', '<=', $limits[1])
            ->count();

            $distribution[] = [
                'range' => $range,
                'count' => $count
            ];
        }

        $this->ageDistributionData = $distribution;
    }

}
