<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->string()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(User::class, 'email', ignoreRecord: true),

                Forms\Components\TextInput::make('ic_number')
                    ->required()
                    ->label('Nombor IC')
                    ->unique(User::class, 'ic_number', ignoreRecord: true)
                    ->length(12)
                    ->numeric(),

                Forms\Components\TextInput::make('phone_number')
                    ->tel()
                    ->required()
                    ->numeric()
                    ->minLength(10)
                    ->maxLength(15),

                Forms\Components\TextInput::make('home_phone')
                    ->tel()
                    ->required()
                    ->numeric()
                    ->minLength(10)
                    ->maxLength(15),

                Forms\Components\Textarea::make('address')
                    ->required()
                    ->string()
                    ->maxLength(255),

                Forms\Components\TextInput::make('age')
                    ->required()
                    ->numeric()
                    ->minValue(18),

                Forms\Components\Select::make('residence_status')
                    ->required()
                    ->options([
                        'kekal' => 'Kekal',
                        'sewa' => 'Sewa',
                    ]),

                    Forms\Components\TextInput::make('password')
                    ->password()
                    ->required(false) // Make it not required
                    ->minLength(8)
                    ->revealable()
                    ->dehydrateStateUsing(function ($state) use (&$data) {
                        // If password is provided, hash it
                        if (filled($state)) {
                            return Hash::make($state);
                        }

                        // Return null to indicate no change if empty (handled in mutateFormDataBeforeCreate)
                        return null;
                    })
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->validationMessages([
                        'min' => 'Kata laluan mesti sekurang-kurangnya 8 aksara.',
                    ]),

                // Also make the confirmation optional
                Forms\Components\TextInput::make('password_confirmation')
                    ->password()
                    ->required(false)
                    ->minLength(8)
                    ->revealable()
                    ->dehydrated(false)
                    ->same('password')
                    ->visible(fn ($get) => filled($get('password'))) // Only show if password is filled
                    ->validationMessages([
                        'min' => 'Pengesahan kata laluan mesti sekurang-kurangnya 8 aksara.',
                        'same' => 'Pengesahan kata laluan tidak sepadan.',
                    ]),

                // If you want to add a role selector for admins
                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->multiple()
                    ->label('Assign Roles'),
            ])
           ;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('No_Ahli')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('ic_number')
                    ->label('Nombor IC')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable(),

                Tables\Columns\TextColumn::make('residence_status')
                    ->label('Status Kediaman'),

                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->label('Peranan'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Add filters if needed
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Add relations if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    // Exclude admin users from the table for non-admin users
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Only filter out user if the current user is not a user
        if (!auth()->user()->hasRole('user')) {
            return $query->whereDoesntHave('roles', function($query) {
                $query->where('name', 'admin');
            });
        }

        return $query;
    }
}
