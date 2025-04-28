<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use App\Models\Transaction;
use App\Models\DeathRecord;
use App\Models\User;
use App\Models\Dependent;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;

class FinancialOverviewWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '60s';
    protected static ?int $sort = 30; // FinancialOverviewWidget

    // Properties to store the date range
    public $startDate;
    public $endDate;

    public function mount()
    {
        // Get date range from session
        $this->startDate = Session::get('dashboard_start_date', Carbon::today()->subDays(29)->format('Y-m-d'));
        $this->endDate = Session::get('dashboard_end_date', Carbon::today()->format('Y-m-d'));
    }

    #[On('dateRangeChanged')]
    public function updateDateRange($data)
    {
        $this->startDate = $data['startDate'];
        $this->endDate = $data['endDate'];

        // In Filament 3.x, use this to refresh stats widget
        $this->dispatch('stats-overview-widget-refresh', [
            'statId' => $this->getStatsOverviewWidgetId(),
        ]);
    }

    protected function getStats(): array
    {
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);

        // Get all-time totals (not affected by date range)
        $allTimePayments = Payment::where('status_id', '1')->sum('amount');
        $allTimeIncome = Transaction::where('type', 'pendapatan')
            ->where('status', 'completed')
            ->sum('amount');
        $allTimeExpenses = Transaction::where('type', 'perbelanjaan')
            ->where('status', 'completed')
            ->sum('amount');
            
        // Calculate all death costs
        $allDeathRecords = DeathRecord::all();
        $allTimeDeathCosts = 0;
        
        foreach ($allDeathRecords as $record) {
            $allTimeDeathCosts += $record->total_cost;
        }
        
        // Add death costs to total expenses
        $allTimeExpenses += $allTimeDeathCosts;
        
        $allTimeRevenue = $allTimePayments + $allTimeIncome;
        $availableFunds = $allTimeRevenue - $allTimeExpenses;

        // Get payments within selected date range
        $rangePayments = Payment::where('status_id', '1')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->sum('amount');

        // Get income transactions within selected date range
        $rangeIncome = Transaction::where('type', 'pendapatan')
            ->where('status', 'completed')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        // Get expense transactions within selected date range
        $rangeExpenses = Transaction::where('type', 'perbelanjaan')
            ->where('status', 'completed')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');
            
        // Get death costs within the date range
        $rangeDeathCosts = 0;
        $deathRecordsInRange = DeathRecord::whereBetween('date_of_death', [$startDate, $endDate])->get();
        
        foreach ($deathRecordsInRange as $record) {
            $rangeDeathCosts += $record->total_cost;
        }
        
        // Add death costs to range expenses
        $totalRangeExpenses = $rangeExpenses + $rangeDeathCosts;

        // Calculate total revenue in range
        $rangeRevenue = $rangePayments + $rangeIncome;

        // Get pending payments (not affected by date range)
        $pendingPayments = Payment::where('status_id', '0')->sum('amount');

        // Format the date range for display
        $rangeDuration = $startDate->diffInDays($endDate) + 1;
        $rangeLabel = $rangeDuration > 1
            ? $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y')
            : $startDate->format('d M Y');

        // Get previous period data for comparison
        $periodLength = $endDate->diffInDays($startDate) + 1;
        $previousStart = $startDate->copy()->subDays($periodLength);
        $previousEnd = $startDate->copy()->subDays(1);

        // Get previous period stats
        $previousPayments = Payment::where('status_id', '1')
            ->whereBetween('paid_at', [$previousStart, $previousEnd])
            ->sum('amount');

        $previousIncome = Transaction::where('type', 'pendapatan')
            ->where('status', 'completed')
            ->whereBetween('transaction_date', [$previousStart, $previousEnd])
            ->sum('amount');

        $previousExpenses = Transaction::where('type', 'perbelanjaan')
            ->where('status', 'completed')
            ->whereBetween('transaction_date', [$previousStart, $previousEnd])
            ->sum('amount');
            
        // Get death costs for previous period
        $previousDeathCosts = 0;
        $previousDeathRecords = DeathRecord::whereBetween('date_of_death', [$previousStart, $previousEnd])->get();
        
        foreach ($previousDeathRecords as $record) {
            $previousDeathCosts += $record->total_cost;
        }
        
        // Add death costs to previous expenses
        $previousTotalExpenses = $previousExpenses + $previousDeathCosts;

        $previousRevenue = $previousPayments + $previousIncome;

        // Calculate trend percentages
        $revenueTrend = $previousRevenue > 0
            ? round((($rangeRevenue - $previousRevenue) / $previousRevenue) * 100, 2)
            : 0;

        $expenseTrend = $previousTotalExpenses > 0
            ? round((($totalRangeExpenses - $previousTotalExpenses) / $previousTotalExpenses) * 100, 2)
            : 0;
            
        // Calculate death costs trend
        $deathCostsTrend = $previousDeathCosts > 0
            ? round((($rangeDeathCosts - $previousDeathCosts) / $previousDeathCosts) * 100, 2)
            : 0;

        // Get death records stats for the time period
        $totalDeaths = $deathRecordsInRange->count();
        
        // Count deaths by category for the period
        $adultDeaths = 0;
        $childDeaths = 0;
        $infantDeaths = 0;
        
        foreach ($deathRecordsInRange as $record) {
            if ($record->deceased_age <= 3) {
                $infantDeaths++;
            } elseif ($record->deceased_age >= 4 && $record->deceased_age <= 6) {
                $childDeaths++;
            } else {
                $adultDeaths++;
            }
        }
        
        // Calculate previous period deaths for trend
        $previousDeathsCount = $previousDeathRecords->count();
        $deathCountTrend = $previousDeathsCount > 0
            ? round((($totalDeaths - $previousDeathsCount) / $previousDeathsCount) * 100, 2)
            : 0;
        
        // Calculate net balance
        $netBalance = $rangeRevenue - $totalRangeExpenses;
        $previousNetBalance = $previousRevenue - $previousTotalExpenses;
        
        $netBalanceTrend = $previousNetBalance != 0
            ? round((($netBalance - $previousNetBalance) / abs($previousNetBalance)) * 100, 2)
            : 0;

        return [
            // Dana Tersedia - Overall funds, not limited by date range
            Stat::make('Jumlah Dana Tersedia', 'RM ' . number_format($availableFunds, 2))
                ->description('Dana bersih selepas perbelanjaan')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary')
                ->chart([
                    $allTimePayments / ($allTimeRevenue ?: 1) * 100,
                    $allTimeIncome / ($allTimeRevenue ?: 1) * 100,
                    $allTimeExpenses / ($allTimeRevenue ?: 1) * 100
                ]),

            // Pendapatan - Revenue within selected date range
            Stat::make('Pendapatan', 'RM ' . number_format($rangeRevenue, 2))
                ->description('Tempoh: ' . $rangeLabel .
                    ($revenueTrend != 0 ? ' (' . ($revenueTrend > 0 ? '+' : '') . $revenueTrend . '%)' : ''))
                ->descriptionIcon($revenueTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color('success'),

            // Perbelanjaan - Expenses within selected date range (excluding death costs)
            Stat::make('Perbelanjaan', 'RM ' . number_format($rangeExpenses, 2))
                ->description('Tempoh: ' . $rangeLabel .
                    ($expenseTrend != 0 ? ' (' . ($expenseTrend > 0 ? '+' : '') . $expenseTrend . '%)' : ''))
                ->descriptionIcon($expenseTrend > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color('danger'),
                
            // Kos Khairat Kematian - Death costs within selected date range
            Stat::make('Kos Khairat Kematian', 'RM ' . number_format($rangeDeathCosts, 2))
                ->description('Tempoh: ' . $rangeLabel .
                    ($deathCostsTrend != 0 ? ' (' . ($deathCostsTrend > 0 ? '+' : '') . $deathCostsTrend . '%)' : ''))
                ->descriptionIcon($deathCostsTrend > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color('warning'),

            // Tunggakan - Pending payments (not affected by date range)
            Stat::make('Tunggakan', 'RM ' . number_format($pendingPayments, 2))
                ->description('Bayaran yang belum diselesaikan')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
                
            // NEW: Jumlah Kematian - Death count in the period
            Stat::make('Jumlah Kematian', $totalDeaths)
                ->description('Dewasa: ' . $adultDeaths . ' | Kanak: ' . $childDeaths . ' | Janin: ' . $infantDeaths .
                    ($deathCountTrend != 0 ? ' (' . ($deathCountTrend > 0 ? '+' : '') . $deathCountTrend . '%)' : ''))
                ->descriptionIcon('heroicon-m-user')
                ->color('gray')
                ->chart([
                    $adultDeaths / ($totalDeaths ?: 1) * 100,
                    $childDeaths / ($totalDeaths ?: 1) * 100,
                    $infantDeaths / ($totalDeaths ?: 1) * 100
                ]),
        ];
    }

    /**
     * Get the widget's ID.
     *
     * @return string
     */
    protected function getStatsOverviewWidgetId(): string
    {
        return $this->getId();
    }
}