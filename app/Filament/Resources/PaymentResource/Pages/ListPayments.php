<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\Payment;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Pembayaran')
                ->color('primary')
                ->icon('heroicon-o-plus'),

            Actions\Action::make('exportPDF')
                ->label('Export PDF')
                ->color('danger')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    // Get all payments with their related data
                    $payments = Payment::with(['user', 'payment_category'])->get();

                    // Check if any payments exist
                    if ($payments->isEmpty()) {
                        Notification::make()
                            ->title('No payments to export')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Generate the PDF with the payments
                    $pdf = Pdf::loadView('pdf.payments', [
                        'payments' => $payments,
                    ]);

                    // Return the PDF download response
                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, 'payments-' . date('Y-m-d') . '.pdf');
                }),

            Actions\Action::make('exportCSV')
                ->label('Export CSV')
                ->color('success')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    // Get all payments with their related data
                    $payments = Payment::with(['user', 'payment_category'])->get();

                    // Check if any payments exist
                    if ($payments->isEmpty()) {
                        Notification::make()
                            ->title('No payments to export')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Generate CSV file
                    $csvFileName = 'payments-' . date('Y-m-d') . '.csv';
                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
                    ];

                    $callback = function () use ($payments) {
                        $file = fopen('php://output', 'w');

                        // Add headers
                        fputcsv($file, [
                            'Member Name',
                            'Payment Category',
                            'Amount (RM)',
                            'Status',
                            'Billcode',
                            'Order ID',
                            'Paid At',
                            'Created At',
                        ]);

                        // Add rows
                        foreach ($payments as $payment) {
                            fputcsv($file, [
                                $payment->user ? $payment->user->name : 'N/A',
                                $payment->payment_category ? $payment->payment_category->category_name : 'N/A',
                                $payment->amount,
                                $payment->status_id == '1' ? 'Paid' : 'Pending',
                                $payment->billcode ?? 'N/A',
                                $payment->order_id ?? 'N/A',
                                $payment->paid_at ? (is_string($payment->paid_at) ? $payment->paid_at : $payment->paid_at->format('Y-m-d H:i:s')) : 'N/A',                                $payment->created_at->format('Y-m-d H:i:s'),
                            ]);
                        }

                        fclose($file);
                    };

                    return response()->stream($callback, 200, $headers);
                }),
        ];
    }
}
