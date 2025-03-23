<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DependentResource\Pages;
use App\Models\Dependent;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DependentResource extends Resource
{
    protected static ?string $model = Dependent::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Dependents';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->label('Member')
                ->relationship('user', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->default(fn() => request()->input('data.user_id'))
                ->disabled(fn() => request()->has('data.user_id'))
                ->validationMessages([
                    'required' => 'Ahli diperlukan.',
                ]),

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
                    'Spouse' => 'Pasangan',
                    'Child' => 'Anak',
                    'Parent' => 'Ibu/Bapa',
                    'Sibling' => 'Adik-beradik',
                    'Other' => 'Lain-lain',
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
                ->unique(User::class, 'ic_number', ignoreRecord: true)
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([Tables\Columns\TextColumn::make('user.name')->label('Member')->sortable()->searchable(), Tables\Columns\TextColumn::make('full_name')->searchable()->sortable(), Tables\Columns\TextColumn::make('relationship')->sortable(), Tables\Columns\TextColumn::make('age')->numeric()->sortable(), Tables\Columns\TextColumn::make('ic_number')->searchable(), Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true)])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')->label('Member')->relationship('user', 'name')->searchable()->preload(),

                Tables\Filters\SelectFilter::make('relationship')->options([
                    'Bapa' => 'Bapa',
                    'Ibu' => 'Ibu',
                    'Pasangan' => 'Pasangan',
                    'Anak' => 'Anak',
                ]),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
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
            'index' => Pages\ListDependents::route('/'),
            'create' => Pages\CreateDependent::route('/create'),
            'edit' => Pages\EditDependent::route('/{record}/edit'),
        ];
    }
}
