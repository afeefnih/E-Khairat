<?php

namespace App\Filament\Resources\PaymentCategoryResource\Pages;

use App\Filament\Resources\PaymentCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\PaymentCategory;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class ListPaymentCategories extends ListRecords
{
    protected static string $resource = PaymentCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Actions\Action::make('exportPDF')
                ->label('Export PDF')
                ->color('danger')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    // Get all payment categories
                    $categories = PaymentCategory::withCount('payments')->get();

                    // Check if any categories exist
                    if ($categories->isEmpty()) {
                        Notification::make()
                            ->title('No payment categories to export')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Generate the PDF with the categories
                    $pdf = Pdf::loadView('pdf.payment-categories', [
                        'categories' => $categories,
                    ]);

                    // Return the PDF download response
                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, 'payment-categories-' . date('Y-m-d') . '.pdf');
                }),

            Actions\Action::make('exportCSV')
                ->label('Export CSV')
                ->color('success')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    // Get all payment categories
                    $categories = PaymentCategory::withCount('payments')->get();

                    // Check if any categories exist
                    if ($categories->isEmpty()) {
                        Notification::make()
                            ->title('No payment categories to export')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Generate CSV file
                    $csvFileName = 'payment-categories-' . date('Y-m-d') . '.csv';
                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
                    ];

                    $callback = function () use ($categories) {
                        $file = fopen('php://output', 'w');

                        // Add headers
                        fputcsv($file, [
                            'Category Name',
                            'Description',
                            'Amount (RM)',
                            'Status',
                            'Number of Payments',
                            'Created At',
                        ]);

                        // Add rows
                        foreach ($categories as $category) {
                            fputcsv($file, [
                                $category->category_name,
                                $category->category_description ?? 'N/A',
                                $category->amount,
                                $category->category_status,
                                $category->payments_count,
                                $category->created_at->format('Y-m-d'),
                            ]);
                        }

                        fclose($file);
                    };

                    return response()->stream($callback, 200, $headers);
                }),
        ];
    }
}
