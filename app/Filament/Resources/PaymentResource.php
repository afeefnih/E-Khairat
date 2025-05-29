<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Model\PaymentCategory;
use Illuminate\Support\Str;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Tables\Actions\BulkActionGroup;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Senarai Pembayaran';
    protected static ?string $navigationGroup = 'Pengurusan Pembayaran';

    protected static ?string $pluralLabel = 'Senarai Pembayaran';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        // Determine if this is an edit form or create form
        $isEditForm = $form->getOperation() === 'edit';

        return $form
        ->schema([
            Forms\Components\Select::make('user_id')
                ->relationship(
                    'user',
                    'name',
                    fn (Builder $query) => $query->whereDoesntHave('roles', function ($q) {
                        $q->where('name', 'admin');
                    })
                )
                ->label('Ahli')
                ->searchable()
                ->preload()
                ->disabled($isEditForm)
                ->required()
                ->validationMessages([
                    'required' => 'Ahli diperlukan.',
                ]),

            Forms\Components\Select::make('payment_category_id')
                ->relationship(
                    'payment_category',
                    'category_name',
                    fn (Builder $query) => $query->where('category_status', 'active')
                )
                ->label('Kategori Pembayaran')
                ->searchable()
                ->preload()
                ->disabled($isEditForm)
                ->required()
                ->validationMessages([
                    'required' => 'Kategori pembayaran diperlukan.',
                ]),

            Forms\Components\TextInput::make('amount')
                ->label('Jumlah (RM)')
                ->disabled($isEditForm)
                ->required()
                ->numeric()
                ->prefix('RM')
                ->validationMessages([
                    'required' => 'Jumlah diperlukan.',
                    'numeric' => 'Jumlah mesti berupa angka.',
                ]),

                Forms\Components\Select::make('status_id')
                ->label('Status')
                ->options([
                    '0' => 'Pending',
                    '1' => 'Selesai',
                ])
                ->required()
                ->live()
                ->afterStateUpdated(function (Get $get, Set $set, $state) {
                    // Generate billcode and order_id when status changes to Paid (1)
                    if ($state === '1') {
                        // Check if billcode is empty
                        if (empty($get('billcode'))) {
                            $set('billcode', 'BILL-' . time() . '-' . Str::random(6));
                        }

                        // Check if order_id is empty
                        if (empty($get('order_id'))) {
                            $set('order_id', 'ORD-' . date('Ymd') . '-' . Str::random(6));
                        }

                        // Set paid_at to current time if it's empty
                        if (empty($get('paid_at'))) {
                            $set('paid_at', now());
                        }
                    } else if ($state === '0') {
                        // Reset fields when status is changed to Pending
                        $set('billcode', null);
                        $set('order_id', null);
                        $set('paid_at', null);
                    }
                })
                ->validationMessages([
                    'required' => 'Status diperlukan.',
                ]),

            Forms\Components\TextInput::make('billcode')
                ->label('Kod Bil')
                ->maxLength(255)
                ->placeholder('Dijana secara automatik apabila pembayaran ditandakan sebagai Dibayar')
                ->helperText('Akan dijana secara automatik apabila pembayaran ditandakan sebagai Dibayar'),

            Forms\Components\TextInput::make('order_id')
                ->label('ID Pesanan')
                ->maxLength(255)
                ->placeholder('Dijana secara automatik apabila pembayaran ditandakan sebagai Dibayar')
                ->helperText('Akan dijana secara automatik apabila pembayaran ditandakan sebagai Dibayar'),

            Forms\Components\DateTimePicker::make('paid_at')
                ->label('Tarikh Dibayar')
                ->placeholder('Pilih tarikh dan masa pembayaran'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Ahli')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_category.category_name')
                    ->label('Kategori Pembayaran')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('MYR')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status_id')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        '0' => 'Pending',
                        '1' => 'Selesai',
                        default => $state,
                    })
                    ->colors([
                        'danger' => fn ($state) => $state == '0',
                        'success' => fn ($state) => $state == '1',
                    ])
                    ->tooltip(fn (string $state): string => match($state) {
                        '0' => 'Pembayaran ini belum dibayar',
                        '1' => 'Pembayaran ini telah dibayar',
                        default => '',
                    }),

                Tables\Columns\TextColumn::make('billcode')
                    ->label('Kod Bil')
                    ->searchable()
                    ->placeholder('Tiada'),

                Tables\Columns\TextColumn::make('order_id')
                    ->label('ID Pesanan')
                    ->searchable()
                    ->placeholder('Tiada'),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Tarikh Dibayar')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Tiada'),

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
                Tables\Filters\SelectFilter::make('status_id')
                    ->label('Status')
                    ->options([
                        '0' => 'Belum Dibayar',
                        '1' => 'Dibayar',
                    ]),

                Tables\Filters\SelectFilter::make('payment_category_id')
                    ->label('Kategori Pembayaran')
                    ->relationship('payment_category', 'category_name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Sunting'),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Bulk action to mark selected payments as paid
                    BulkAction::make('mark-as-paid')
                        ->label('Tandakan Sebagai Dibayar')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                if ($record->status_id == '0') {
                                    $record->status_id = '1';
                                    $record->billcode = $record->billcode ?? 'BILL-' . time() . '-' . Str::random(6);
                                    $record->order_id = $record->order_id ?? 'ORD-' . date('Ymd') . '-' . Str::random(6);
                                    $record->paid_at = $record->paid_at ?? now();
                                    $record->save();
                                }
                            });

                            Notification::make()
                                ->title(count($records) . ' pembayaran telah ditandakan sebagai dibayar')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    // Bulk action to mark selected payments as unpaid
                    BulkAction::make('mark-as-unpaid')
                    ->label('Tandakan Sebagai Belum Dibayar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(function (Collection $records) {
                        $records->each(function ($record) {
                            if ($record->status_id == '1') {
                                $record->status_id = '0';
                                $record->paid_at = null;
                                $record->billcode = null; // Reset billcode
                                $record->order_id = null; // Reset order_id
                                $record->save();
                            }
                        });

                        Notification::make()
                            ->title(count($records) . ' pembayaran telah ditandakan sebagai belum dibayar')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->deselectRecordsAfterCompletion(),

                    // Add CSV Export Bulk Action
                    BulkAction::make('export-csv')
                        ->label('Eksport ke CSV')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(function (Collection $records) {
                            // Check if any records are selected
                            if ($records->isEmpty()) {
                                Notification::make()
                                    ->title('Tiada pembayaran untuk dieksport')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            // Generate CSV file
                            $csvFileName = 'pembayaran-' . date('Y-m-d') . '.csv';
                            $headers = [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
                            ];

                            $callback = function () use ($records) {
                                $file = fopen('php://output', 'w');

                                // Add headers
                                fputcsv($file, [
                                    'Nama Ahli',
                                    'Kategori Pembayaran',
                                    'Jumlah (RM)',
                                    'Status',
                                    'Kod Bil',
                                    'ID Pesanan',
                                    'Tarikh Dibayar',
                                    'Tarikh Cipta',
                                ]);

                                // Add rows
                                foreach ($records as $payment) {
                                    fputcsv($file, [
                                        $payment->user ? $payment->user->name : 'Tiada',
                                        $payment->payment_category ? $payment->payment_category->category_name : 'Tiada',
                                        $payment->amount,
                                        $payment->status_id == '1' ? 'Dibayar' : 'Belum Dibayar',
                                        $payment->billcode ?? 'Tiada',
                                        $payment->order_id ?? 'Tiada',
                                        $payment->paid_at ? (is_string($payment->paid_at) ? $payment->paid_at : $payment->paid_at->format('Y-m-d H:i:s')) : 'Tiada',
                                        $payment->created_at->format('Y-m-d H:i:s'),
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
                                    ->title('Tiada pembayaran untuk dieksport')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            try {
                                // Generate the PDF
                                $pdf = Pdf::loadView('pdf.payments', [
                                    'payments' => $records,
                                ])->setPaper('A4', 'potrait');

                                return response()->streamDownload(function () use ($pdf) {
                                    echo $pdf->output();
                                }, 'pembayaran-' . date('Y-m-d') . '.pdf');
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Ralat menjana PDF')
                                    ->danger()
                                    ->body($e->getMessage())
                                    ->send();
                            }
                        }),
                ]),
            ]);
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }

    // Filter out inactive payment categories in table view
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('payment_category', function (Builder $query) {
                $query->where('category_status', 'active');
            });
    }
}
