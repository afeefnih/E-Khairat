<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Dependent;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class UserStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '60s';

    // Add a property to determine if we want detailed stats
    public bool $detailed = false;

    protected function getStats(): array
    {
        // Get total users excluding admins
        $totalMembers = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        })->count();

        // Count total dependents
        $totalDependents = Dependent::count();

        // Get newly registered members this month
        $newMembers = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Basic stats that appear everywhere
        $stats = [
            Stat::make('Total Members', $totalMembers)
                ->description('Jumlah ahli yang berdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Total Dependents', $totalDependents)
                ->description('Jumlah tanggungan')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),

            Stat::make('New Members (This Month)', $newMembers)
                ->description('Ahli baru bulan ini')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('success'),
        ];

        // Add detailed stats only if detailed flag is true
        if ($this->detailed) {
            // Get members with deceased status
            $deceasedMembers = User::whereHas('deathRecord')->count();

            // Get users with kekal residence status
            $kekalUsers = User::where('residence_status', 'kekal')->count();

            // Get users with sewa residence status
            $sewaUsers = User::where('residence_status', 'sewa')->count();

            // Calculate percentage for residence status
            $kekalPercentage = $totalMembers > 0 ? round(($kekalUsers / $totalMembers) * 100) : 0;
            $sewaPercentage = $totalMembers > 0 ? round(($sewaUsers / $totalMembers) * 100) : 0;

            // Add additional stats
            $stats[] = Stat::make('Deceased Members', $deceasedMembers)
                ->description('Ahli yang telah meninggal dunia')
                ->descriptionIcon('heroicon-m-heart')
                ->color('danger');

            $stats[] = Stat::make('Permanent Residence', "{$kekalUsers} ({$kekalPercentage}%)")
                ->description('Ahli status kediaman kekal')
                ->descriptionIcon('heroicon-m-home')
                ->color('primary');

            $stats[] = Stat::make('Rental Residence', "{$sewaUsers} ({$sewaPercentage}%)")
                ->description('Ahli status kediaman sewa')
                ->descriptionIcon('heroicon-m-home-modern')
                ->color('warning');
        }

        return $stats;
    }
}
