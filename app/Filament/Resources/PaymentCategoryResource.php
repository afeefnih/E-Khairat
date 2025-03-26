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
use Illuminate\Database\Eloquent\Builder;

class PaymentCategoryResource extends Resource
{
    protected static ?string $model = PaymentCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Payment Categories';
    protected static ?string $navigationGroup = 'Payments';
    protected static ?int  $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('category_name')
                    ->label('Category Name')
                    ->required()
                    ->maxLength(255)
                    ->validationMessages([
                        'required' => 'Nama kategori diperlukan.',
                        'max' => 'Nama kategori tidak boleh melebihi 255 aksara.',
                    ]),

                Forms\Components\Textarea::make('category_description')
                    ->label('Description')
                    ->nullable()
                    ->maxLength(1000)
                    ->columnSpanFull()
                    ->validationMessages([
                        'max' => 'Penerangan tidak boleh melebihi 1000 aksara.',
                    ]),

                Forms\Components\TextInput::make('amount')
                    ->label('Amount (RM)')
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
                        'active' => 'Active',
                        'inactive' => 'Inactive',
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
                    ->label('Category Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
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
                    ->sortable(),

                Tables\Columns\TextColumn::make('payments_count')
                    ->label('Payments')
                    ->counts('payments')
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
                Tables\Filters\SelectFilter::make('category_status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->disabled(fn (PaymentCategory $record) => $record->payments()->count() > 0),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
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
