<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentCategoryResource\Pages;
use App\Filament\Resources\PaymentCategoryResource\RelationManagers;
use App\Models\PaymentCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Tables\Actions\DeleteBulkAction;

class PaymentCategoryResource extends Resource
{
    protected static ?string $model = PaymentCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = ' Kutipan Sumbangan ';
    protected static ?string $navigationGroup = 'Pembayaran';
    protected static ?int  $navigationSort = 2;

    protected static ?string $pluralLabel = 'Senarai Kutipan Sumbangan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('category_name')
                    ->label('Nama Kategori')
                    ->required()
                    ->maxLength(255)
                    ->validationMessages([
                        'required' => 'Nama kategori diperlukan.',
                        'max' => 'Nama kategori tidak boleh melebihi 255 aksara.',
                    ]),

                Forms\Components\Textarea::make('category_description')
                    ->label('Penerangan')
                    ->nullable()
                    ->maxLength(1000)
                    ->columnSpanFull()
                    ->validationMessages([
                        'max' => 'Penerangan tidak boleh melebihi 1000 aksara.',
                    ]),

                Forms\Components\TextInput::make('amount')
                    ->label('Jumlah (RM)')
                    ->required()
                    ->numeric()
                    ->prefix('RM')
                    ->validationMessages([
                        'required' => 'Jumlah diperlukan.',
                        'numeric' => 'Jumlah mesti berupa angka.',
                    ]),

                Forms\Components\Select::make('category_status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                    ])
                    ->default('active')
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category_name')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('MYR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('category_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('payments_count')
                    ->label('Pembayaran')
                    ->counts('payments')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarikh Cipta')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Tarikh Kemaskini')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->id !== 1), // Hide delete button for ID 1
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Padam Terpilih')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records) {
                            $filteredRecords = $records->reject(fn ($record) => $record->id === 1); // Exclude ID 1
                            if ($filteredRecords->isEmpty()) {
                                Notification::make()
                                    ->title('kategori pembayaran tidak sah untuk dipadam')
                                    ->danger()
                                    ->send();
                                return;
                            }
                            $filteredRecords->each->delete();
                            Notification::make()
                                ->title('Kategori pembayaran berjaya dipadam')
                                ->success()
                                ->send();
                        }),

                    // Add CSV Export Bulk Action
                    BulkAction::make('export-csv')
                    ->label('Eksport ke CSV')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function (Collection $records) {
                        // Check if any records are selected
                        if ($records->isEmpty()) {
                            Notification::make()
                                ->title('Tiada kategori pembayaran untuk dieksport')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Generate CSV file
                        $csvFileName = 'kategori-pembayaran-' . date('Y-m-d') . '.csv';
                        $headers = [
                            'Content-Type' => 'text/csv',
                            'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
                        ];

                        $callback = function () use ($records) {
                            $file = fopen('php://output', 'w');

                            // Add headers
                            fputcsv($file, [
                                'Nama Kategori',
                                'Penerangan',
                                'Jumlah (RM)',
                                'Status',
                                'Bilangan Pembayaran',
                                'Tarikh Cipta',
                            ]);

                            // Add rows
                            foreach ($records as $record) {
                                fputcsv($file, [
                                    $record->category_name,
                                    $record->category_description ?? 'Tiada',
                                    $record->amount,
                                    $record->category_status === 'active' ? 'Aktif' : 'Tidak Aktif',
                                    $record->payments()->count(),
                                    $record->created_at->format('Y-m-d'),
                                ]);
                            }

                            fclose($file);
                        };

                        return response()->stream($callback, 200, $headers);
                    }),

                // Add PDF Export Bulk Action
                BulkAction::make('export-pdf')
                    ->label('Eksport ke PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('danger')
                    ->action(function (Collection $records) {
                        // Check if any records are selected
                        if ($records->isEmpty()) {
                            Notification::make()
                                ->title('Tiada kategori pembayaran untuk dieksport')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Generate the PDF
                        $pdf = Pdf::loadView('pdf.payment-categories', [
                            'categories' => $records,
                        ]);

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, 'kategori-pembayaran-' . date('Y-m-d') . '.pdf');
                    }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PaymentsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentCategories::route('/'),
            'create' => Pages\CreatePaymentCategory::route('/create'),
            'edit' => Pages\EditPaymentCategory::route('/{record}/edit'),
        ];
    }
}
