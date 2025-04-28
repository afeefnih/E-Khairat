<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use App\Models\Payment;
use App\Models\DeathRecord; // Add this import
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;

class FinancialChartsWidget extends ChartWidget
{
    protected static ?string $heading = 'Income vs Expenses';
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '300px';

    //sort
    protected static ?int $sort = 40; // FinancialChartsWidget

    // Properties to store the date range
    public $startDate;
    public $endDate;

    public function mount(): void
    {
        // Get date range from session
        $this->startDate = Session::get('dashboard_start_date', Carbon::today()->subDays(29)->format('Y-m-d'));
        $this->endDate = Session::get('dashboard_end_date', Carbon::today()->format(format: 'Y-m-d'));
    }

    #[On('dateRangeChanged')]
    public function updateDateRange($data)
    {
        $this->startDate = $data['startDate'];
        $this->endDate = $data['endDate'];

        // Use this method in Filament 3.x to refresh the chart
        $this->updateChartData();
    }

    protected function getData(): array
    {
        $data = $this->getIncomeVsExpenseData();

        return [
            'datasets' => [
                [
                    'label' => 'Payment Income',
                    'data' => $data['payment_income'],
                    'backgroundColor' => 'rgba(54, 162, 235, 0.7)',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Transaction Income',
                    'data' => $data['transaction_income'],
                    'backgroundColor' => 'rgba(75, 192, 192, 0.7)',
                    'borderColor' => 'rgb(75, 192, 192)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Expenses',
                    'data' => $data['expenses'],
                    'backgroundColor' => 'rgba(255, 99, 132, 0.7)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Death Costs',
                    'data' => $data['death_costs'],
                    'backgroundColor' => 'rgba(255, 159, 64, 0.7)',
                    'borderColor' => 'rgb(255, 159, 64)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Total Income',
                    'data' => $data['total_income'],
                    'backgroundColor' => 'rgba(0, 0, 0, 0)', // Transparent
                    'borderColor' => 'rgba(0, 200, 0, 1)',
                    'borderWidth' => 2,
                    'type' => 'line',
                    'fill' => false,
                    'tension' => 0.1,
                ],
                [
                    'label' => 'Total Expenses',
                    'data' => $data['total_expenses'],
                    'backgroundColor' => 'rgba(0, 0, 0, 0)', // Transparent
                    'borderColor' => 'rgba(255, 0, 0, 1)',
                    'borderWidth' => 2,
                    'type' => 'line',
                    'fill' => false,
                    'tension' => 0.1,
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    private function getIncomeVsExpenseData(): array
    {
        // Convert start and end dates to Carbon instances
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);

        // Calculate the number of data points based on date range
        $diffInDays = $end->diffInDays($start);

        if ($diffInDays <= 31) {
            // For ranges under a month, show daily data
            return $this->getDailyData($start, $end);
        } else if ($diffInDays <= 365) {
            // For ranges under a year, show monthly data
            return $this->getMonthlyData($start, $end);
        } else {
            // For ranges over a year, show quarterly data
            return $this->getQuarterlyData($start, $end);
        }
    }

    private function getDailyData($start, $end)
    {
        $labels = [];
        $paymentIncome = [];
        $transactionIncome = [];
        $totalIncome = [];
        $expenses = [];
        $deathCosts = [];
        $totalExpenses = [];

        $current = clone $start;

        while ($current <= $end) {
            $labels[] = $current->format('d M');

            // Get income from Transactions for this day
            $dayTransactionIncome = Transaction::where('type', 'pendapatan')
                ->where('status', 'completed')
                ->whereDate('transaction_date', $current)
                ->sum('amount');

            // Get income from Payments for this day
            $dayPaymentIncome = Payment::where('status_id', '1')
                ->whereNotNull('paid_at')
                ->whereDate('paid_at', $current)
                ->sum('amount');

            // Calculate total income
            $dayTotalIncome = $dayTransactionIncome + $dayPaymentIncome;

            // Get expenses for this day
            $dayExpenses = Transaction::where('type', 'perbelanjaan')
                ->where('status', 'completed')
                ->whereDate('transaction_date', $current)
                ->sum('amount');

            // Get death costs for this day
            $dayDeathCosts = 0;
            $dayDeathRecords = DeathRecord::whereDate('date_of_death', $current)->get();

            foreach ($dayDeathRecords as $record) {
                $dayDeathCosts += $record->total_cost;
            }

            // Calculate total expenses
            $dayTotalExpenses = $dayExpenses + $dayDeathCosts;

            $paymentIncome[] = $dayPaymentIncome ?: 0;
            $transactionIncome[] = $dayTransactionIncome ?: 0;
            $totalIncome[] = $dayTotalIncome ?: 0;
            $expenses[] = $dayExpenses ?: 0;
            $deathCosts[] = $dayDeathCosts ?: 0;
            $totalExpenses[] = $dayTotalExpenses ?: 0;

            $current->addDay();
        }

        return [
            'labels' => $labels,
            'payment_income' => $paymentIncome,
            'transaction_income' => $transactionIncome,
            'total_income' => $totalIncome,
            'expenses' => $expenses,
            'death_costs' => $deathCosts,
            'total_expenses' => $totalExpenses,
        ];
    }

    private function getMonthlyData($start, $end)
    {
        $labels = [];
        $paymentIncome = [];
        $transactionIncome = [];
        $totalIncome = [];
        $expenses = [];
        $deathCosts = [];
        $totalExpenses = [];

        // Start from the beginning of the start month
        $current = $start->copy()->startOfMonth();

        // End at the end of the end month
        $endMonth = $end->copy()->endOfMonth();

        while ($current <= $endMonth) {
            $labels[] = $current->format('M Y');

            // Get income from Transactions for this month
            $monthTransactionIncome = Transaction::where('type', 'pendapatan')
                ->where('status', 'completed')
                ->whereYear('transaction_date', $current->year)
                ->whereMonth('transaction_date', $current->month)
                ->sum('amount');

            // Get income from Payments for this month
            $monthPaymentIncome = Payment::where('status_id', '1')
                ->whereNotNull('paid_at')
                ->whereYear('paid_at', $current->year)
                ->whereMonth('paid_at', $current->month)
                ->sum('amount');

            // Calculate total income
            $monthTotalIncome = $monthTransactionIncome + $monthPaymentIncome;

            // Get expenses for this month
            $monthExpenses = Transaction::where('type', 'perbelanjaan')
                ->where('status', 'completed')
                ->whereYear('transaction_date', $current->year)
                ->whereMonth('transaction_date', $current->month)
                ->sum('amount');

            // Get death costs for this month
            $monthDeathCosts = 0;
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();
            $monthDeathRecords = DeathRecord::whereBetween('date_of_death', [$monthStart, $monthEnd])->get();

            foreach ($monthDeathRecords as $record) {
                $monthDeathCosts += $record->total_cost;
            }

            // Calculate total expenses
            $monthTotalExpenses = $monthExpenses + $monthDeathCosts;

            $paymentIncome[] = $monthPaymentIncome ?: 0;
            $transactionIncome[] = $monthTransactionIncome ?: 0;
            $totalIncome[] = $monthTotalIncome ?: 0;
            $expenses[] = $monthExpenses ?: 0;
            $deathCosts[] = $monthDeathCosts ?: 0;
            $totalExpenses[] = $monthTotalExpenses ?: 0;

            $current->addMonth();
        }

        return [
            'labels' => $labels,
            'payment_income' => $paymentIncome,
            'transaction_income' => $transactionIncome,
            'total_income' => $totalIncome,
            'expenses' => $expenses,
            'death_costs' => $deathCosts,
            'total_expenses' => $totalExpenses,
        ];
    }

    private function getQuarterlyData($start, $end)
    {
        $labels = [];
        $paymentIncome = [];
        $transactionIncome = [];
        $totalIncome = [];
        $expenses = [];
        $deathCosts = [];
        $totalExpenses = [];

        // Start from the beginning of the start quarter
        $current = $start->copy()->startOfQuarter();

        // End at the end of the end quarter
        $endQuarter = $end->copy()->endOfQuarter();

        while ($current <= $endQuarter) {
            $quarterName = 'Q' . ceil($current->month / 3) . ' ' . $current->year;
            $labels[] = $quarterName;

            $quarterStart = $current->copy();
            $quarterEnd = $current->copy()->endOfQuarter();

            // Get income from Transactions for this quarter
            $quarterTransactionIncome = Transaction::where('type', 'pendapatan')
                ->where('status', 'completed')
                ->whereBetween('transaction_date', [$quarterStart, $quarterEnd])
                ->sum('amount');

            // Get income from Payments for this quarter
            $quarterPaymentIncome = Payment::where('status_id', '1')
                ->whereNotNull('paid_at')
                ->whereBetween('paid_at', [$quarterStart, $quarterEnd])
                ->sum('amount');

            // Calculate total income
            $quarterTotalIncome = $quarterTransactionIncome + $quarterPaymentIncome;

            // Get expenses for this quarter
            $quarterExpenses = Transaction::where('type', 'perbelanjaan')
                ->where('status', 'completed')
                ->whereBetween('transaction_date', [$quarterStart, $quarterEnd])
                ->sum('amount');

            // Get death costs for this quarter
            $quarterDeathCosts = 0;
            $quarterDeathRecords = DeathRecord::whereBetween('date_of_death', [$quarterStart, $quarterEnd])->get();

            foreach ($quarterDeathRecords as $record) {
                $quarterDeathCosts += $record->total_cost;
            }

            // Calculate total expenses
            $quarterTotalExpenses = $quarterExpenses + $quarterDeathCosts;

            $paymentIncome[] = $quarterPaymentIncome ?: 0;
            $transactionIncome[] = $quarterTransactionIncome ?: 0;
            $totalIncome[] = $quarterTotalIncome ?: 0;
            $expenses[] = $quarterExpenses ?: 0;
            $deathCosts[] = $quarterDeathCosts ?: 0;
            $totalExpenses[] = $quarterTotalExpenses ?: 0;

            $current->addQuarter();
        }

        return [
            'labels' => $labels,
            'payment_income' => $paymentIncome,
            'transaction_income' => $transactionIncome,
            'total_income' => $totalIncome,
            'expenses' => $expenses,
            'death_costs' => $deathCosts,
            'total_expenses' => $totalExpenses,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
