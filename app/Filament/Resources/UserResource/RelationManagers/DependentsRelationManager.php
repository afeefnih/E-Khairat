<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Dependent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\User;

class DependentsRelationManager extends RelationManager
{
    protected static string $relationship = 'dependents';

    protected static ?string $recordTitleAttribute = 'full_name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('full_name')
                    ->required()
                    ->maxLength(255)
                    ->validationMessages([
                        'required' => 'Nama penuh diperlukan.',
                        'max' => 'Nama penuh tidak boleh melebihi 255 aksara.',
                    ]),

                Forms\Components\Select::make('relationship')
                    ->required()
                    ->options([
                        'Bapa' => 'Bapa',
                        'Ibu' => 'Ibu',
                        'Pasangan' => 'Pasangan',
                        'Anak' => 'Anak',
                    ])
                    ->validationMessages([
                        'required' => 'Hubungan diperlukan.',
                    ]),

                Forms\Components\TextInput::make('age')
                    ->required()
                    ->numeric()
                    ->validationMessages([
                        'required' => 'Umur diperlukan.',
                        'numeric' => 'Umur mesti berupa angka.',
                    ]),

                    Forms\Components\TextInput::make('ic_number')
                    ->required()
                    ->label('Nombor IC')
                    ->unique(Dependent::class, 'ic_number', ignoreRecord: true)
                    ->length(12)
                    ->numeric()
                    ->validationMessages([
                        'required' => 'Nombor IC diperlukan.',
                        'digits' => 'Nombor IC mesti 12 digit.',
                        'unique' => 'Nombor IC telah digunakan.',
                        'numeric' => 'Nombor IC mesti berupa angka.',
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('full_name')
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('relationship')
                    ->sortable(),

                Tables\Columns\TextColumn::make('age')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ic_number')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('relationship')
                    ->options([
                        'Spouse' => 'Pasangan',
                        'Child' => 'Anak',
                        'Parent' => 'Ibu/Bapa',
                        'Sibling' => 'Adik-beradik',
                        'Other' => 'Lain-lain',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Dependent added')
                            ->body('The dependent has been added successfully.'),
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Dependent updated')
                            ->body('The dependent has been updated successfully.'),
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Dependent deleted')
                            ->body('The dependent has been deleted successfully.'),
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

}
