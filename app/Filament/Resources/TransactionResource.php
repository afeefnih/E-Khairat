<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

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
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('Jenis Transaksi')
                    ->options([
                        'pendapatan' => 'Pendapatan',
                        'perbelanjaan' => 'Perbelanjaan',
                    ])
                    ->required()
                    ->native(false),

                Forms\Components\TextInput::make('name')
                    ->label('Nama Transaksi')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->label('Penerangan')
                    ->nullable()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('amount')
                    ->label('Jumlah (RM)')
                    ->required()
                    ->numeric()
                    ->prefix('RM'),

                Forms\Components\DatePicker::make('transaction_date')
                    ->label('Tarikh Transaksi')
                    ->required()
                    ->default(now()),

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
                Tables\Columns\TextColumn::make('transaction_date')
                    ->label('Tarikh')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendapatan' => 'success',
                        'perbelanjaan' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pendapatan' => 'Pendapatan',
                        'perbelanjaan' => 'Perbelanjaan',
                    }),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('MYR')
                    ->color(fn (Transaction $record): string => $record->isIncome() ? 'success' : 'danger')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'completed' => 'Selesai',
                        'pending' => 'Belum Selesai',
                        'cancelled' => 'Batal',
                    }),
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
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
