<?php

namespace App\Filament\Widgets;

use App\Models\DeathRecord;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;

class DeathRecordsTimelineWidget extends ChartWidget
{
    protected static ?string $heading = 'Rekod Kematian';
    protected static ?int $sort = 70; // DeathRecordsTimelineWidget
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '300px';

    // Properties to store the date range
    public $startDate;
    public $endDate;

    public function mount(): void
    {
        // Get date range from session
        $this->startDate = Session::get('dashboard_start_date', Carbon::today()->subDays(29)->format('Y-m-d'));
        $this->endDate = Session::get('dashboard_end_date', Carbon::today()->format('Y-m-d'));

        // Update heading to reflect the current date range
        $this->updateHeading();
    }

    #[On('dateRangeChanged')]
    public function updateDateRange($data)
    {
        $this->startDate = $data['startDate'];
        $this->endDate = $data['endDate'];

        // Update the heading to reflect the new date range
        $this->updateHeading();

        // Refresh the chart
        $this->updateChartData();
    }

    protected function updateHeading()
    {
        $start = Carbon::parse($this->startDate)->format('d M Y');
        $end = Carbon::parse($this->endDate)->format('d M Y');

        // Set the heading to include the date range
        static::$heading = "Rekod Kematian ($start - $end)";
    }

    protected function getData(): array
    {
        $data = $this->getDeathRecordsData();

        return [
            'datasets' => [
                [
                    'label' => 'Ahli Utama',
                    'data' => $data['member_deaths'],
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Tanggungan',
                    'data' => $data['dependent_deaths'],
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'borderWidth' => 1
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    private function getDeathRecordsData(): array
    {
        // Convert start and end dates to Carbon instances
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);

        // Calculate the number of days in the range
        $diffInDays = $end->diffInDays($start);

        if ($diffInDays <= 31) {
            // For ranges under a month, show daily data
            return $this->getDailyDeathData($start, $end);
        } else if ($diffInDays <= 365) {
            // For ranges under a year, show monthly data
            return $this->getMonthlyDeathData($start, $end);
        } else {
            // For ranges over a year, show quarterly data
            return $this->getQuarterlyDeathData($start, $end);
        }
    }

    private function getDailyDeathData($start, $end)
    {
        $labels = [];
        $memberDeaths = [];
        $dependentDeaths = [];

        $current = clone $start;

        while ($current <= $end) {
            $dayLabel = $current->format('d M');

            // Count deaths for this day
            $memberDeathCount = DeathRecord::where('deceased_type', 'App\\Models\\User')
                ->whereDate('date_of_death', $current)
                ->count();

            $dependentDeathCount = DeathRecord::where('deceased_type', 'App\\Models\\Dependent')
                ->whereDate('date_of_death', $current)
                ->count();

            $labels[] = $dayLabel;
            $memberDeaths[] = $memberDeathCount;
            $dependentDeaths[] = $dependentDeathCount;

            $current->addDay();
        }

        return [
            'labels' => $labels,
            'member_deaths' => $memberDeaths,
            'dependent_deaths' => $dependentDeaths,
        ];
    }

    private function getMonthlyDeathData($start, $end)
    {
        $labels = [];
        $memberDeaths = [];
        $dependentDeaths = [];

        // Start from the beginning of the start month
        $current = $start->copy()->startOfMonth();

        // End at the end of the end month
        $endMonth = $end->copy()->endOfMonth();

        while ($current <= $endMonth) {
            $monthLabel = $current->format('M Y');

            // Count deaths for this month
            $memberDeathCount = DeathRecord::where('deceased_type', 'App\\Models\\User')
                ->whereYear('date_of_death', $current->year)
                ->whereMonth('date_of_death', $current->month)
                ->count();

            $dependentDeathCount = DeathRecord::where('deceased_type', 'App\\Models\\Dependent')
                ->whereYear('date_of_death', $current->year)
                ->whereMonth('date_of_death', $current->month)
                ->count();

            $labels[] = $monthLabel;
            $memberDeaths[] = $memberDeathCount;
            $dependentDeaths[] = $dependentDeathCount;

            $current->addMonth();
        }

        return [
            'labels' => $labels,
            'member_deaths' => $memberDeaths,
            'dependent_deaths' => $dependentDeaths,
        ];
    }

    private function getQuarterlyDeathData($start, $end)
    {
        $labels = [];
        $memberDeaths = [];
        $dependentDeaths = [];

        // Start from the beginning of the start quarter
        $current = $start->copy()->startOfQuarter();

        // End at the end of the end quarter
        $endQuarter = $end->copy()->endOfQuarter();

        while ($current <= $endQuarter) {
            $quarterLabel = 'Q' . ceil($current->month / 3) . ' ' . $current->year;

            $quarterStart = $current->copy();
            $quarterEnd = $current->copy()->endOfQuarter();

            // Count deaths for this quarter
            $memberDeathCount = DeathRecord::where('deceased_type', 'App\\Models\\User')
                ->whereBetween('date_of_death', [$quarterStart, $quarterEnd])
                ->count();

            $dependentDeathCount = DeathRecord::where('deceased_type', 'App\\Models\\Dependent')
                ->whereBetween('date_of_death', [$quarterStart, $quarterEnd])
                ->count();

            $labels[] = $quarterLabel;
            $memberDeaths[] = $memberDeathCount;
            $dependentDeaths[] = $dependentDeathCount;

            $current->addQuarter();
        }

        return [
            'labels' => $labels,
            'member_deaths' => $memberDeaths,
            'dependent_deaths' => $dependentDeaths,
        ];
    }
}
