<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use App\Models\PaymentCategory;
use Filament\Widgets\ChartWidget;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CollectionEfficiencyWidget extends ChartWidget
{
    protected static ?string $heading = 'Peratusan Pembayaran Mengikut Kategori'; // "Payment Percentage by Category"
    protected static ?int $sort = 80;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '200px';

    protected function getData(): array
    {
        $data = $this->getCollectionEfficiencyData();

        // If no data, provide default values to ensure the chart renders
        if (empty($data['percentagePaid'])) {
            return [
                'datasets' => [
                    [
                        'label' => 'Peratus Dibayar',
                        'data' => [100],
                        'backgroundColor' => ['rgba(200, 200, 200, 0.7)'],
                        'borderColor' => ['rgb(200, 200, 200)'],
                        'borderWidth' => 1
                    ],
                ],
                'labels' => ['Tiada Data'],
            ];
        }

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
                        'rgba(255, 205, 86, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 99, 132, 0.7)',
                    ],
                    'borderColor' => [
                        'rgb(75, 192, 192)',
                        'rgb(153, 102, 255)',
                        'rgb(255, 159, 64)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 99, 132)',
                        'rgb(255, 205, 86)',
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
        // Get active payment categories
        $categories = PaymentCategory::where('category_status', 'active')
            ->get();

        $labels = [];
        $percentagePaid = [];

        foreach ($categories as $category) {
            // Count total payments for this category (without date filtering)
            $totalPayments = Payment::where('payment_category_id', $category->id)
                ->count();

            // Count paid payments (status_id = 1) for this category
            $paidPayments = Payment::where('payment_category_id', $category->id)
                ->where('status_id', '1')
                ->count();

            // If no payments at all for this category, show as 0% collection
            if ($totalPayments == 0) {
                if ($category->amount > 0) {
                    // This is a valid payment category with no payments yet
                    $labels[] = $category->category_name . ' (0/0)';
                    $percentagePaid[] = 0;
                }
                continue;
            }

            // Calculate percentage
            $percentage = round(($paidPayments / $totalPayments) * 100);

            // Always add categories that have data
            $labels[] = $category->category_name . ' (' . $paidPayments . '/' . $totalPayments . ')';
            $percentagePaid[] = $percentage;
        }

        // If still no data at all, create a static example
        if (empty($labels)) {
            // Create sample data
            foreach (['Yuran Bulanan', 'Yuran Tahunan', 'Khairat Kematian'] as $index => $name) {
                $labels[] = $name . ' (Sampel)';
                $percentagePaid[] = ($index + 1) * 25;
            }
        }

        return [
            'labels' => $labels,
            'percentagePaid' => $percentagePaid,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
            'scales' => [
                'x' => [ 'display' => false ],
                'y' => [ 'display' => false ],
            ],
        ];
    }
}
