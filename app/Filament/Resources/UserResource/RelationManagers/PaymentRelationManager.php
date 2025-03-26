<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Payment;
use App\Models\PaymentCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use App\Models\User;

class PaymentRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('payment_category_id'),

            Forms\Components\TextInput::make('order_id')
                ->required()
                ->maxLength(255),

            Forms\Components\Select::make('status_id')
                ->required()
                ->options([
                    '1' => 'Paid',
                    '0' => 'Pending',
                ]),

            Forms\Components\TextInput::make('amount')
                ->required()
                ->numeric()
                ->prefix('RM'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payment_category.category_name')
                    ->label('Payment Category')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('order_id')
                    ->label('Order ID')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->money('MYR')
                    ->sortable(),

                Tables\Columns\IconColumn::make('status_id')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Payment Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_id')
                    ->label('Status')
                    ->options([
                        '1' => 'Paid',
                        '0' => 'Pending',
                    ]),
            ])
            ->actions([Tables\Actions\ViewAction::make(), Tables\Actions\EditAction::make()])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }



}
