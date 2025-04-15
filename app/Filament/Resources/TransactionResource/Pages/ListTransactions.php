<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\Transaction;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Actions\Action::make('exportPDF')
                ->label('Export PDF')
                ->color('danger')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    // Get all transactions with their related data
                    $transactions = Transaction::with(['user'])->get();

                    // Check if any transactions exist
                    if ($transactions->isEmpty()) {
                        Notification::make()
                            ->title('No transactions to export')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Generate the PDF with the transactions
                    $pdf = Pdf::loadView('pdf.transactions', [
                        'transactions' => $transactions,
                    ]);

                    // Return the PDF download response
                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, 'transactions-' . date('Y-m-d') . '.pdf');
                }),

            Actions\Action::make('exportCSV')
                ->label('Export CSV')
                ->color('success')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    // Get all transactions with their related data
                    $transactions = Transaction::with(['user'])->get();

                    // Check if any transactions exist
                    if ($transactions->isEmpty()) {
                        Notification::make()
                            ->title('No transactions to export')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Generate CSV file
                    $csvFileName = 'transactions-' . date('Y-m-d') . '.csv';
                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
                    ];

                    $callback = function () use ($transactions) {
                        $file = fopen('php://output', 'w');

                        // Add headers
                        fputcsv($file, [
                            'Date',
                            'Transaction Name',
                            'Member',
                            'Type',
                            'Amount (RM)',
                            'Payment Method',
                            'Status',
                            'Description',
                            'Created At',
                        ]);

                        // Add rows
                        foreach ($transactions as $transaction) {
                            $type = match ($transaction->type) {
                                'pendapatan' => 'Pendapatan',
                                'perbelanjaan' => 'Perbelanjaan',
                                default => $transaction->type,
                            };

                            $status = match ($transaction->status) {
                                'completed' => 'Selesai',
                                'pending' => 'Belum Selesai',
                                'cancelled' => 'Batal',
                                default => $transaction->status,
                            };

                            fputcsv($file, [
                                $transaction->transaction_date ? $transaction->transaction_date->format('Y-m-d') : 'N/A',
                                $transaction->name,
                                $transaction->user ? $transaction->user->name : 'N/A',
                                $type,
                                $transaction->amount,
                                $transaction->payment_method ?? 'N/A',
                                $status,
                                $transaction->description ?? 'N/A',
                                $transaction->created_at->format('Y-m-d'),
                            ]);
                        }

                        fclose($file);
                    };

                    return response()->stream($callback, 200, $headers);
                }),
        ];
    }
}
