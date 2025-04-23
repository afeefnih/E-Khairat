<?php

namespace App\Filament\Resources\PaymentCategoryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use App\Filament\Resources\PaymentResource;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $recordTitleAttribute = 'order_id';

    // Translate the title
    protected static ?string $title = 'Pembayaran';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('order_id')
                ->label('ID Pesanan')
                ->required()
                ->maxLength(255)
                ->validationMessages([
                    'required' => 'ID pesanan diperlukan.',
                    'max' => 'ID pesanan tidak boleh melebihi 255 aksara.',
                ]),

            Forms\Components\Select::make('status_id')
                ->label('Status')
                ->required()
                ->options([
                    '1' => 'Dibayar',
                    '0' => 'Belum Dibayar',
                ])
                ->validationMessages([
                    'required' => 'Status diperlukan.',
                ]),

            Forms\Components\TextInput::make('amount')
                ->label('Jumlah')
                ->required()
                ->numeric()
                ->prefix('RM')
                ->validationMessages([
                    'required' => 'Jumlah diperlukan.',
                    'numeric' => 'Jumlah mesti berupa angka.',
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Ahli')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => $record->user ? route('filament.admin.resources.users.edit', $record->user) : null),

                Tables\Columns\TextColumn::make('order_id')
                    ->label('ID Pesanan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('MYR')
                    ->sortable(),

                Tables\Columns\IconColumn::make('status_id')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn (bool $state): string => $state ? 'Dibayar' : 'Belum Dibayar')
                    ->sortable(),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Tarikh Pembayaran')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarikh Cipta')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_id')
                    ->label('Status')
                    ->options([
                        '1' => 'Dibayar',
                        '0' => 'Belum Dibayar',
                    ]),
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make()
                //     ->label('Tambah Pembayaran')
                //     ->successNotification(
                //         Notification::make()
                //             ->success()
                //             ->title('Pembayaran Ditambah')
                //             ->body('Pembayaran telah berjaya ditambah.')
                //     ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat'),

                Tables\Actions\EditAction::make()
                    ->label('Sunting')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Pembayaran Dikemaskini')
                            ->body('Pembayaran telah berjaya dikemaskini.')
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make()
                    //     ->label('Padam Terpilih')
                    //     ->successNotification(
                    //         Notification::make()
                    //             ->success()
                    //             ->title('Pembayaran Dipadam')
                    //             ->body('Pembayaran yang dipilih telah berjaya dipadam.')
                    //     ),
                ]),
            ]);
    }
}
