<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
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

    protected static ?string $pluralLabel = 'Senarai Transaksi';
    protected static ?string $navigationGroup = 'Kewangan'; // Changed from 'Payments' to 'Kewangan'
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
                ->native(false)
                ->validationMessages([
                    'required' => 'Jenis transaksi diperlukan.',
                ]),

            Forms\Components\TextInput::make('name')
                ->label('Nama Transaksi')
                ->required()
                ->maxLength(255)
                ->validationMessages([
                    'required' => 'Nama transaksi diperlukan.',
                    'max' => 'Nama transaksi tidak boleh melebihi 255 aksara.',
                ]),

            Forms\Components\Textarea::make('description')
                ->label('Penerangan')
                ->nullable()
                ->columnSpanFull(),

            Forms\Components\Select::make('user_id')
                ->label('Ahli')
                ->relationship('user', 'name')
                ->searchable()
                ->preload()
                ->nullable()
                ->helperText('Abaikan jika transaksi bukan daripada ahli'),

            Forms\Components\TextInput::make('amount')
                ->label('Jumlah (RM)')
                ->required()
                ->numeric()
                ->prefix('RM')
                ->validationMessages([
                    'required' => 'Jumlah transaksi diperlukan.',
                    'numeric' => 'Jumlah mesti berupa angka.',
                ]),

            Forms\Components\DatePicker::make('transaction_date')
                ->label('Tarikh Transaksi')
                ->required()
                ->default(now())
                ->validationMessages([
                    'required' => 'Tarikh transaksi diperlukan.',
                ]),

            Forms\Components\TextInput::make('payment_method')
                ->label('Kaedah Pembayaran')
                ->nullable()
                ->maxLength(255),

            Forms\Components\FileUpload::make('receipt_path')
                ->label('Resit')
                ->directory('transaction-receipts')
                ->downloadable()
                ->openable()
                ->preserveFilenames()
                ->acceptedFileTypes(['image/*', 'application/pdf'])
                ->maxSize(5120), // 5MB size limit

            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'completed' => 'Selesai',
                    'pending' => 'Pending',
                    'cancelled' => 'Batal',
                ])
                ->default('completed')
                ->required()
                ->validationMessages([
                    'required' => 'Status transaksi diperlukan.',
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_date')
                    ->label('Tarikh')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Ahli')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color(
                        fn(string $state): string => match ($state) {
                            'pendapatan' => 'success',
                            'perbelanjaan' => 'danger',
                            default => 'gray',
                        },
                    )
                    ->formatStateUsing(
                        fn(string $state): string => match ($state) {
                            'pendapatan' => 'Pendapatan',
                            'perbelanjaan' => 'Perbelanjaan',
                            default => $state,
                        },
                    ),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('MYR')
                    ->color(fn(Transaction $record): string => $record->isIncome() ? 'success' : 'danger')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Kaedah Pembayaran')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(
                        fn(string $state): string => match ($state) {
                            'completed' => 'success',
                            'pending' => 'warning',
                            'cancelled' => 'danger',
                            default => 'gray',
                        },
                    )
                    ->formatStateUsing(
                        fn(string $state): string => match ($state) {
                            'completed' => 'Selesai',
                            'pending' => 'Pending',
                            'cancelled' => 'Batal',
                            default => $state,
                        },
                    ),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarikh Cipta')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Ahli')
                    ->relationship('user', 'name'),

                Tables\Filters\Filter::make('transaction_date')
                    ->label('Tempoh Tarikh')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Dari'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Hingga'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['from'],
                                fn($query) => $query->whereDate('transaction_date', '>=', $data['from']),
                            )
                            ->when(
                                $data['until'],
                                fn($query) => $query->whereDate('transaction_date', '<=', $data['until']),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('view_receipt')
                    ->label('Lihat Resit')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn(Transaction $record) => $record->receipt_path ? asset('storage/' . $record->receipt_path) : null)
                    ->openUrlInNewTab()
                    ->visible(fn(Transaction $record) => $record->receipt_path !== null),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Padam Terpilih'),

                    // Update CSV Export Bulk Action with Malay translation
                    BulkAction::make('export-csv')
                        ->label('Eksport ke CSV')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(function (Collection $records) {
                            // Check if any records are selected
                            if ($records->isEmpty()) {
                                Notification::make()
                                    ->title('Tiada transaksi untuk dieksport')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            // Generate CSV file
                            $csvFileName = 'transaksi-' . date('Y-m-d') . '.csv';
                            $headers = [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
                            ];

                            $callback = function () use ($records) {
                                $file = fopen('php://output', 'w');

                                // Add headers with Malay translation
                                fputcsv($file, [
                                    'Tarikh',
                                    'Nama Transaksi',
                                    'Ahli',
                                    'Jenis',
                                    'Jumlah (RM)',
                                    'Kaedah Pembayaran',
                                    'Status',
                                    'Penerangan',
                                    'Tarikh Cipta',
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
                                        'pending' => 'Pending',
                                        'cancelled' => 'Batal',
                                        default => $transaction->status,
                                    };

                                    fputcsv($file, [
                                        $transaction->transaction_date ? $transaction->transaction_date->format('Y-m-d') : 'Tiada',
                                        $transaction->name,
                                        $transaction->user ? $transaction->user->name : 'Tiada',
                                        $type,
                                        $transaction->amount,
                                        $transaction->payment_method ?? 'Tiada',
                                        $status,
                                        $transaction->description ?? 'Tiada',
                                        $transaction->created_at->format('Y-m-d'),
                                    ]);
                                }

                                fclose($file);
                            };

                            return response()->stream($callback, 200, $headers);
                        }),

                    // Update PDF Export Bulk Action with Malay translation
                    BulkAction::make('export-pdf')
                        ->label('Eksport ke PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('danger')
                        ->action(function (Collection $records) {
                            // Check if any records are selected
                            if ($records->isEmpty()) {
                                Notification::make()
                                    ->title('Tiada transaksi untuk dieksport')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            try {
                                // Generate the PDF
                                $pdf = Pdf::loadView('pdf.transactions', [
                                    'transactions' => $records,
                                ]);

                                return response()->streamDownload(function () use ($pdf) {
                                    echo $pdf->output();
                                }, 'transaksi-' . date('Y-m-d') . '.pdf');
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Ralat menjana PDF')
                                    ->danger()
                                    ->body($e->getMessage())
                                    ->send();
                            }
                        }),
                ]),
            ])
            ->defaultSort('transaction_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
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
