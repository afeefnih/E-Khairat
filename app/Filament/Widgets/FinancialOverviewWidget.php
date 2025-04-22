<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class FinancialOverviewWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '60s';
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Get all completed payments (status_id = 1)
        $totalPayments = Payment::where('status_id', '1')->sum('amount');

        // Get all pending payments (status_id = 0)
        $pendingPayments = Payment::where('status_id', '0')->sum('amount');

        // Get income transactions (pendapatan)
        $totalIncome = Transaction::where('type', 'pendapatan')
            ->where('status', 'completed')
            ->sum('amount');

        // Get expense transactions (perbelanjaan)
        $totalExpenses = Transaction::where('type', 'perbelanjaan')
            ->where('status', 'completed')
            ->sum('amount');

        // Calculate total revenue (payments + income)
        $totalRevenue = $totalPayments + $totalIncome;

        // Calculate total funds available
        $availableFunds = $totalRevenue - $totalExpenses;

        // Get this month's revenue
        $thisMonthRevenue = Payment::where('status_id', '1')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        $thisMonthRevenue += Transaction::where('type', 'pendapatan')
            ->where('status', 'completed')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');

        // Get this month's expenses
        $thisMonthExpenses = Transaction::where('type', 'perbelanjaan')
            ->where('status', 'completed')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');

        // Calculate trend percentages
        $lastMonthRevenue = $this->getLastMonthRevenue();
        $lastMonthExpenses = $this->getLastMonthExpenses();

        $revenueTrend = $lastMonthRevenue > 0
            ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 2)
            : 0;

        $expenseTrend = $lastMonthExpenses > 0
            ? round((($thisMonthExpenses - $lastMonthExpenses) / $lastMonthExpenses) * 100, 2)
            : 0;

        return [
            // Dana Tersedia - Keep as is, most important financial metric
            Stat::make('Jumlah Dana Tersedia', 'RM ' . number_format($availableFunds, 2))
                ->description('Dana bersih selepas perbelanjaan')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary')
                ->chart([
                    $totalPayments / ($totalRevenue ?: 1) * 100,
                    $totalIncome / ($totalRevenue ?: 1) * 100,
                    $totalExpenses / ($totalRevenue ?: 1) * 100
                ]),

            // Consolidated Pendapatan - Combining total and monthly
            Stat::make('Pendapatan', 'RM ' . number_format($totalRevenue, 2))
                ->description('Bulan ini: RM ' . number_format($thisMonthRevenue, 2) .
                    ($revenueTrend != 0 ? ' (' . ($revenueTrend > 0 ? '+' : '') . $revenueTrend . '%)' : ''))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            // Consolidated Perbelanjaan - Combining total and monthly
            Stat::make('Perbelanjaan', 'RM ' . number_format($totalExpenses, 2))
                ->description('Bulan ini: RM ' . number_format($thisMonthExpenses, 2) .
                    ($expenseTrend != 0 ? ' (' . ($expenseTrend > 0 ? '+' : '') . $expenseTrend . '%)' : ''))
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            // Tunggakan - Keep as is, important financial metric
            Stat::make('Tunggakan', 'RM ' . number_format($pendingPayments, 2))
                ->description('Bayaran yang belum diselesaikan')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }

    private function getLastMonthRevenue(): float
    {
        $lastMonth = now()->subMonth();

        $lastMonthPayments = Payment::where('status_id', '1')
            ->whereMonth('paid_at', $lastMonth->month)
            ->whereYear('paid_at', $lastMonth->year)
            ->sum('amount');

        $lastMonthIncome = Transaction::where('type', 'pendapatan')
            ->where('status', 'completed')
            ->whereMonth('transaction_date', $lastMonth->month)
            ->whereYear('transaction_date', $lastMonth->year)
            ->sum('amount');

        return $lastMonthPayments + $lastMonthIncome;
    }

    private function getLastMonthExpenses(): float
    {
        $lastMonth = now()->subMonth();

        return Transaction::where('type', 'perbelanjaan')
            ->where('status', 'completed')
            ->whereMonth('transaction_date', $lastMonth->month)
            ->whereYear('transaction_date', $lastMonth->year)
            ->sum('amount');
    }
}
