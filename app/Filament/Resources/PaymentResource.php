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
    protected static ?string $navigationLabel = 'Payment List';
    protected static ?string $navigationGroup = 'Payments';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        // Determine if this is an edit form or create form
        $isEditForm = $form->getOperation() === 'edit';

        return $form
        ->schema([
            Forms\Components\Select::make('user_id')
            ->relationship('user', 'name')
            ->label('User')
            ->disabled($isEditForm)
            ->required(),

            Forms\Components\Select::make('payment_category_id')
            ->relationship(
                'payment_category',
                'category_name',
                fn (Builder $query) => $query->where('category_status', 'active')
            )
            ->label('Payment Category')
            ->disabled($isEditForm)
            ->required(),

            Forms\Components\TextInput::make('amount')
                ->disabled($isEditForm)
                ->required()
                ->numeric(),

            Forms\Components\Select::make('status_id')
                ->label('Status')
                ->options([
                    '0' => 'Pending',
                    '1' => 'Paid',
                ])
                ->required()
                ->live()
                ->afterStateUpdated(function (Get $get, Set $set, $state) {
                    // Only generate billcode and order_id when status changes to Paid (1)
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
                    }
                }),

            Forms\Components\TextInput::make('billcode')
                ->maxLength(255)
                ->placeholder('Auto-generated when payment is marked as Paid')
                ->helperText('Will be auto-generated when payment is marked as Paid'),

            Forms\Components\TextInput::make('order_id')
                ->maxLength(255)
                ->placeholder('Auto-generated when payment is marked as Paid')
                ->helperText('Will be auto-generated when payment is marked as Paid'),

            Forms\Components\DateTimePicker::make('paid_at'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_category.category_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                    Tables\Columns\BadgeColumn::make('status_id')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => $state == '1' ? 'Paid' : 'Pending')
                    ->colors([
                        'danger' => fn ($state) => $state == '0',
                        'success' => fn ($state) => $state == '1',
                    ]),
                Tables\Columns\TextColumn::make('billcode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('order_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('paid_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
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

                             $callback = function () use ($records) {
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
                                 foreach ($records as $payment) {
                                     fputcsv($file, [
                                         $payment->user ? $payment->user->name : 'N/A',
                                         $payment->payment_category ? $payment->payment_category->category_name : 'N/A',
                                         $payment->amount,
                                         $payment->status_id == '1' ? 'Paid' : 'Pending',
                                         $payment->billcode ?? 'N/A',
                                         $payment->order_id ?? 'N/A',
                                         $payment->paid_at ? (is_string($payment->paid_at) ? $payment->paid_at : $payment->paid_at->format('Y-m-d H:i:s')) : 'N/A',                                         $payment->created_at->format('Y-m-d H:i:s'),
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
                                     ->title('No payments to export')
                                     ->danger()
                                     ->send();
                                 return;
                             }

                             // Generate the PDF
                             $pdf = Pdf::loadView('pdf.payments', [
                                 'payments' => $records,
                             ]);

                             return response()->streamDownload(function () use ($pdf) {
                                 echo $pdf->output();
                             }, 'payments-' . date('Y-m-d') . '.pdf');
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
