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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Filament\Forms\Get;
use Filament\Forms\Set;

class PaymentRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public function form(Form $form): Form
    {
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

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                // Filter to only show payments with active payment categories
                $query->whereHas('payment_category', function (Builder $query) {
                    $query->where('category_status', 'active');
                });
            })
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
