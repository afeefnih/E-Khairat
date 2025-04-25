<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use App\Models\PaymentCategory;
use Filament\Widgets\ChartWidget;

use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;

class CollectionEfficiencyWidget extends ChartWidget
{
    protected static ?string $heading = 'Peratusan Pembayaran Mengikut Kategori'; // "Payment Percentage by Category"
    protected static ?int $sort = 50;
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
    }

    #[On('dateRangeChanged')]
    public function updateDateRange($data)
    {
        $this->startDate = $data['startDate'];
        $this->endDate = $data['endDate'];
        $this->updateChartData();
    }

    protected function getData(): array
    {
        $data = $this->getCollectionEfficiencyData();

        return [
            'datasets' => [
                [
                    'label' => 'Peratus Dibayar',
                    'data' => $data['percentagePaid'],
                    'backgroundColor' => [
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 99, 132, 0.7)',
                    ],
                    'borderColor' => [
                        'rgb(75, 192, 192)',
                        'rgb(153, 102, 255)',
                        'rgb(255, 159, 64)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 99, 132)',
                    ],
                    'borderWidth' => 1
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    private function getCollectionEfficiencyData(): array
    {
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);

        // Based on your PaymentCategoryResource structure
        $categories = PaymentCategory::where('category_status', 'active')
            ->get();

        $labels = [];
        $percentagePaid = [];

        foreach ($categories as $category) {
            // Count total payments for this category within date range
            $totalPayments = Payment::where('payment_category_id', $category->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            // Count paid payments for this category within date range
            $paidPayments = Payment::where('payment_category_id', $category->id)
                ->where('status_id', '1')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            // Calculate percentage
            $percentage = $totalPayments > 0
                ? round(($paidPayments / $totalPayments) * 100)
                : 0;

            // Create a label that includes the raw data
            $labels[] = $category->category_name . ' (' . $paidPayments . '/' . $totalPayments . ')';
            $percentagePaid[] = $percentage;
        }

        return [
            'labels' => $labels,
            'percentagePaid' => $percentagePaid,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
        ];
    }
}
