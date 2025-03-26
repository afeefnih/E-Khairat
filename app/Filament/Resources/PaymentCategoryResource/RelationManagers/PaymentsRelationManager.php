<?php

namespace App\Filament\Resources\PaymentCategoryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use App\Filament\Resources\PaymentCategoryResource\RelationManagers;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $recordTitleAttribute = 'order_id';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('order_id')->required()->maxLength(255),

            Forms\Components\Select::make('status_id')
                ->required()
                ->options([
                    '1' => 'Paid',
                    '0' => 'Pending',
                ]),

            Forms\Components\TextInput::make('amount')->required()->numeric()->prefix('RM'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([Tables\Columns\TextColumn::make('user.name')->label('Member')->searchable()->sortable(), Tables\Columns\TextColumn::make('order_id')->label('Order ID')->searchable()->sortable(), Tables\Columns\TextColumn::make('amount')->money('MYR')->sortable(), Tables\Columns\IconColumn::make('status_id')->label('Status')->boolean()->sortable(), Tables\Columns\TextColumn::make('paid_at')->label('Payment Date')->dateTime()->sortable()])
            ->filters([
                Tables\Filters\SelectFilter::make('status_id')
                    ->label('Status')
                    ->options([
                        '1' => 'Paid',
                        '0' => 'Pending',
                    ]),
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([Tables\Actions\ViewAction::make(), Tables\Actions\EditAction::make()])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function getRelations(): array
    {
        return [RelationManagers\PaymentsRelationManager::class];
    }
}
