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

class FinancialOverviewWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '60s';
    protected static ?int $sort = 30; // FinancialOverviewWidget

    protected function getStats(): array
    {
        // All-time totals
        $allTimePayments = Payment::where('status_id', '1')->sum('amount');
        $allTimeIncome = Transaction::where('type', 'pendapatan')
            ->where('status', 'completed')
            ->sum('amount');

        // Only include death records for members (exclude non_member)
        $allDeathRecords = DeathRecord::where('deceased_type', '!=', 'non_member')->get();
        $allTimeDeathCosts = 0;
        foreach ($allDeathRecords as $record) {
            $allTimeDeathCosts += $record->total_cost;
        }
        $allTimeExpensesTransaksi = Transaction::where('type', 'perbelanjaan')
            ->where('status', 'completed')
            ->sum('amount');
        $allTimeExpenses = $allTimeExpensesTransaksi + $allTimeDeathCosts;
        $allTimeRevenue = $allTimePayments + $allTimeIncome;
        $availableFunds = $allTimeRevenue - $allTimeExpenses;

        // All-time pending payments
        $pendingPayments = Payment::where('status_id', '0')->sum('amount');

        // All-time death stats
        $totalDeaths = $allDeathRecords->count();
        $adultDeaths = 0;
        $childDeaths = 0;
        $infantDeaths = 0;
        foreach ($allDeathRecords as $record) {
            if ($record->deceased_age <= 3) {
                $infantDeaths++;
            } elseif ($record->deceased_age >= 4 && $record->deceased_age <= 6) {
                $childDeaths++;
            } else {
                $adultDeaths++;
            }
        }

        $allTimeTransactions = Transaction::count();

        return [

            // Pendapatan - All-time revenue
            Stat::make('Pendapatan', 'RM ' . number_format($allTimeRevenue, 2))
                ->description('Jumlah pendapatan sepanjang masa')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            // Perbelanjaan - All-time expenses (including death costs)
            Stat::make('Perbelanjaan', 'RM ' . number_format($allTimeExpenses, 2))
                ->description('Transaksi: RM ' . number_format($allTimeExpensesTransaksi, 2) . ' | Khairat Kematian: RM ' . number_format($allTimeDeathCosts, 2))
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),



            // Tunggakan - Pending payments (not affected by date range)
            Stat::make('Tunggakan', 'RM ' . number_format($pendingPayments, 2))
                ->description('Bayaran yang belum diselesaikan')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

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


            // Jumlah Transaksi - Total transactions
            Stat::make('Jumlah Transaksi', $allTimeTransactions)
                ->description('Jumlah transaksi kewangan (pendapatan & perbelanjaan)')
                ->descriptionIcon('heroicon-m-arrows-right-left')
                ->color('info'),


            // Jumlah Kematian - All-time death count
            Stat::make('Jumlah Kematian', $totalDeaths)
                ->description('Dewasa: ' . $adultDeaths . ' | Kanak: ' . $childDeaths . ' | Janin: ' . $infantDeaths)
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
