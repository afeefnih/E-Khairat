<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Dependent;
use App\Models\Payment;
use App\Models\DeathRecord;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;

class MemberStatisticsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '60s';
    protected static ?int $sort = 20;

    // Properties to store the date range
    public $startDate;
    public $endDate;
    public $preset;

    public function mount()
    {
        // Get date range from session
        $this->startDate = Session::get('dashboard_start_date', Carbon::today()->subDays(29)->format('Y-m-d'));
        $this->endDate = Session::get('dashboard_end_date', Carbon::today()->format('Y-m-d'));
        $this->preset = Session::get('dashboard_date_preset', 'last30days');
    }

    #[On('dateRangeChanged')]
    public function updateDateRange($data)
    {
        $this->startDate = $data['startDate'];
        $this->endDate = $data['endDate'];
        $this->preset = $data['preset'] ?? 'custom';

        // Log for debugging
        \Log::info("MemberStatisticsWidget updating date range: {$this->startDate} to {$this->endDate} (preset: {$this->preset})");

        // In Filament 3.x, use this to refresh stats widget
        $this->dispatch('stats-overview-widget-refresh', [
            'statId' => $this->getStatsOverviewWidgetId(),
        ]);
    }

    protected function getStats(): array
    {
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);

        \Log::info("Generating member stats for date range: {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}");

        // Base query for all members (users with role 'user'), ONLY alive members
        $memberQuery = User::whereHas('roles', function ($query) {
            $query->where('name', 'user');
        })->whereDoesntHave('deathRecord');

        // Now only count alive members
        $totalMembers = $memberQuery->count();

        // Get new members during selected date range - only alive new members
        $newMembers = User::whereHas('roles', function ($query) {
            $query->where('name', 'user');
        })
        ->whereDoesntHave('deathRecord')
        ->whereBetween('registration_date', [$startDate, $endDate])
        ->count();

        $kekalMembers = (clone $memberQuery)
            ->where('residence_status', 'kekal')
            ->count();

        $sewaMembers = (clone $memberQuery)
            ->where('residence_status', 'sewa')
            ->count();

        // Calculate percentages based on total members
        $kekalPercentage = $totalMembers > 0 ? round(($kekalMembers / $totalMembers) * 100) : 0;
        $sewaPercentage = $totalMembers > 0 ? round(($sewaMembers / $totalMembers) * 100) : 0;

        // Get total dependents (only alive dependents)
        $totalDependents = Dependent::whereDoesntHave('deathRecord')->count();

        // Family size distribution
        $singleMembers = (clone $memberQuery)
            ->has('dependents', '=', 0)
            ->count();

        $smallFamilies = (clone $memberQuery)
            ->has('dependents', '>=', 1)
            ->has('dependents', '<=', 3)
            ->count();

        $largeFamilies = (clone $memberQuery)
            ->has('dependents', '>', 3)
            ->count();

        // Get average dependents per member
        $avgDependentsPerMember = $totalMembers > 0
            ? round(($totalDependents / $totalMembers), 1)
            : 0;

        // Calculate percentages for family sizes
        $singlePercentage = $totalMembers > 0 ? round(($singleMembers / $totalMembers) * 100) : 0;
        $smallFamilyPercentage = $totalMembers > 0 ? round(($smallFamilies / $totalMembers) * 100) : 0;
        $largeFamilyPercentage = $totalMembers > 0 ? round(($largeFamilies / $totalMembers) * 100) : 0;

        // Build description text based on whether we're in "all time" view or a specific period
        $newMembersDescription = $this->preset === 'allTime'
            ? 'Jumlah ahli baru'
            : 'Ahli baru dalam tempoh';

        return [
            // First row
            Stat::make('Jumlah Ahli', $totalMembers)
                ->description($newMembersDescription . ': ' . $newMembers)
                ->descriptionIcon('heroicon-m-user-plus')
                ->chart([5, 7, 9, 8, 6, $totalMembers % 10])
                ->color('primary'),

            Stat::make('Jumlah Tanggungan', $totalDependents)
                ->description('Purata ' . $avgDependentsPerMember . ' per ahli')
                ->descriptionIcon('heroicon-m-user-group')
                ->chart([3, 5, 2, 7, 4, 6])
                ->color('info'),

            Stat::make('Saiz Keluarga', 'Purata: ' . $avgDependentsPerMember . ' tanggungan')
                ->description('Tiada: ' . $singleMembers . ' | 1-3: ' . $smallFamilies . ' | >3: ' . $largeFamilies)
                ->descriptionIcon('heroicon-m-users')
                ->chart([$singlePercentage, $smallFamilyPercentage, $largeFamilyPercentage])
                ->color('success'),

            Stat::make('Status Kediaman', 'Kekal: ' . $kekalPercentage . '% | Sewa: ' . $sewaPercentage . '%')
                ->description('Kekal: ' . $kekalMembers . ' | Sewa: ' . $sewaMembers)
                ->descriptionIcon('heroicon-m-home')
                ->chart([$kekalPercentage, $sewaPercentage])
                ->color('warning'),
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
