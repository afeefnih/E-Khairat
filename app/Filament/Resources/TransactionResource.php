<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use App\Models\User; // Use User model, not Ahli
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions;
use Filament\Tables\Filters;
use Filament\Tables\Actions\BulkAction;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Transaksi';
    protected static ?string $modelLabel = 'Transaksi';
    protected static ?string $navigationGroup = 'Payments';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('type')
                ->label('Jenis Transaksi')
                ->options([
                    'pendapatan' => 'Pendapatan',
                    'perbelanjaan' => 'Perbelanjaan',
                ])
                ->required()
                ->native(false),

            Forms\Components\TextInput::make('name')->label('Nama Transaksi')->required()->maxLength(255),

            Forms\Components\Textarea::make('description')->label('Penerangan')->nullable()->columnSpanFull(),

            // Updated the Ahli field to use user relationship
            // Updated the Ahli field to use user relationship with a hint
            Forms\Components\Select::make('user_id')
                ->label('Ahli')
                ->relationship('user', 'name') // Use user relationship with name field
                ->searchable()
                ->preload()
                ->nullable()
                ->helperText('Abaikan jika transaksi bukan daripada ahli'),

            Forms\Components\TextInput::make('amount')->label('Jumlah (RM)')->required()->numeric()->prefix('RM'),

            Forms\Components\DatePicker::make('transaction_date')->label('Tarikh Transaksi')->required()->default(now()),

            Forms\Components\TextInput::make('payment_method')->label('Kaedah Pembayaran')->nullable()->maxLength(255),

            Forms\Components\FileUpload::make('receipt_path')
                ->label('Resit')
                ->directory('transaction-receipts')
                ->downloadable()
                ->openable()
                ->preserveFilenames()
                ->acceptedFileTypes(['image/*', 'application/pdf']),

            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'completed' => 'Selesai',
                    'pending' => 'Belum Selesai',
                    'cancelled' => 'Batal',
                ])
                ->default('completed')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_date')->label('Tarikh')->date()->sortable(),

                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable(),

                // Updated to use user.name instead of ahli.name
                Tables\Columns\TextColumn::make('user.name')->label('Ahli')->searchable()->toggleable()->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color(
                        fn(string $state): string => match ($state) {
                            'pendapatan' => 'success',
                            'perbelanjaan' => 'danger',
                        },
                    )
                    ->formatStateUsing(
                        fn(string $state): string => match ($state) {
                            'pendapatan' => 'Pendapatan',
                            'perbelanjaan' => 'Perbelanjaan',
                        },
                    ),

                Tables\Columns\TextColumn::make('amount')->label('Jumlah')->money('MYR')->color(fn(Transaction $record): string => $record->isIncome() ? 'success' : 'danger')->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(
                        fn(string $state): string => match ($state) {
                            'completed' => 'success',
                            'pending' => 'warning',
                            'cancelled' => 'danger',
                        },
                    )
                    ->formatStateUsing(
                        fn(string $state): string => match ($state) {
                            'completed' => 'Selesai',
                            'pending' => 'Belum Selesai',
                            'cancelled' => 'Batal',
                        },
                    ),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Jenis Transaksi')
                    ->options([
                        'pendapatan' => 'Pendapatan',
                        'perbelanjaan' => 'Perbelanjaan',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'completed' => 'Selesai',
                        'pending' => 'Belum Selesai',
                        'cancelled' => 'Batal',
                    ]),

                // Updated to use user_id
                Tables\Filters\SelectFilter::make('user_id')->label('Ahli')->relationship('user', 'name'),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    // Add CSV Export Bulk Action
                    BulkAction::make('export-csv')
                        ->label('Export to CSV')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(function (Collection $records) {
                            // Check if any records are selected
                            if ($records->isEmpty()) {
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

                            $callback = function () use ($records) {
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
                                foreach ($records as $transaction) {
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

                    // Add PDF Export Bulk Action
                    BulkAction::make('export-pdf')
                        ->label('Export to PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('danger')
                        ->action(function (Collection $records) {
                            // Check if any records are selected
                            if ($records->isEmpty()) {
                                Notification::make()
                                    ->title('No transactions to export')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            // Generate the PDF
                            $pdf = Pdf::loadView('pdf.transactions', [
                                'transactions' => $records,
                            ]);

                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, 'transactions-' . date('Y-m-d') . '.pdf');
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
