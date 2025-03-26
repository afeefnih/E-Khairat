<?php

namespace App\Filament\Resources;


use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\DependentResource;
use App\Filament\Resources\PaymentCategoryResource;
use App\Filament\Resources\PaymentResource;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;
use App\Models\Dependent;



class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'Ahli';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Users';




    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->validationMessages([
                        'required' => 'Nama diperlukan.',
                        'string' => 'Nama mesti berupa teks.',
                        'max' => 'Nama tidak boleh melebihi 255 aksara.',
                    ]),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(User::class, 'email', ignoreRecord: true)
                    ->validationMessages([
                        'required' => 'Emel diperlukan.',
                        'email' => 'Emel mesti alamat emel yang sah.',
                        'unique' => 'Emel telah digunakan.',
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

                Forms\Components\TextInput::make('phone_number')
                    ->tel()
                    ->required()
                    ->numeric()
                    ->minLength(10)
                    ->maxLength(15)
                    ->validationMessages([
                        'required' => 'Nombor telefon diperlukan.',
                        'numeric' => 'Nombor telefon mesti berupa angka.',
                        'min_digits' => 'Nombor telefon mesti sekurang-kurangnya 10 digit.',
                        'max_digits' => 'Nombor telefon tidak boleh melebihi 15 digit.',
                    ]),

                Forms\Components\TextInput::make('home_phone')
                    ->tel()
                    ->required()
                    ->numeric()
                    ->minLength(10)
                    ->maxLength(15)
                    ->validationMessages([
                        'required' => 'Nombor telefon rumah diperlukan.',
                        'numeric' => 'Nombor telefon rumah mesti berupa angka.',
                        'min_digits' => 'Nombor telefon rumah mesti sekurang-kurangnya 10 digit.',
                        'max_digits' => 'Nombor telefon rumah tidak boleh melebihi 15 digit.',
                    ]),

                Forms\Components\Textarea::make('address')
                    ->required()
                    ->string()
                    ->maxLength(255)
                    ->validationMessages([
                        'required' => 'Alamat diperlukan.',
                        'string' => 'Alamat mesti berupa teks.',
                        'max' => 'Alamat tidak boleh melebihi 255 aksara.',
                    ]),

                Forms\Components\TextInput::make('age')
                    ->required()
                    ->numeric()
                    ->minValue(18)
                    ->validationMessages([
                        'required' => 'Umur diperlukan.',
                        'integer' => 'Umur mesti berupa angka.',
                        'min' => 'Umur mesti sekurang-kurangnya 18 tahun.',
                    ]),

                Forms\Components\Select::make('residence_status')
                    ->required()
                    ->options([
                        'kekal' => 'Kekal',
                        'sewa' => 'Sewa',
                    ])
                    ->validationMessages([
                        'required' => 'Status kediaman diperlukan.',
                        'in' => 'Status kediaman mesti salah satu daripada: kekal, sewa.',
                    ]),

                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required(false) // Make it not required
                    ->minLength(8)
                    ->revealable()
                    ->dehydrateStateUsing(function ($state) {
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

                // Role selector for admins
                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->multiple()
                    ->label('Peranan'),
            ]);
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

                // Add a column to show number of dependents
                Tables\Columns\TextColumn::make('dependents_count')
                    ->label('Dependents')
                    ->counts('dependents')
                    ->badge(),

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
                // Add a button to view this user's dependents
                Tables\Actions\Action::make('dependents')
                    ->label('View Dependents')
                    ->icon('heroicon-o-users')
                    ->color('success')
                    ->url(fn (User $record) => DependentResource::getUrl('index', ['tableFilters[user_id][value]' => $record->id])),

                // Add a button to create a dependent for this user
                Tables\Actions\Action::make('addDependent')
                    ->label('Add Dependent')
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->url(fn (User $record) => DependentResource::getUrl('create', ['data[user_id]' => $record->id])),
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
            RelationManagers\DependentsRelationManager::class,
            RelationManagers\PaymentRelationManager::class, // Add this line

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
