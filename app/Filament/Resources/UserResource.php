<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\Textcolumn;
use Illuminate\Support\Facades\Hash;
use Filament\Tables\Actions\DeleteAction;



class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                TextInput::make('No_Ahli')
                    ->label('No. Ahli')
                    ->disabled()
                    ->dehydrated(true) // Ensure it's included in form data
                    ->visible(fn ($record) => $record !== null) // Only show for existing records
                    ->helperText('Automatically generated upon creation'),

                TextInput::make('ic_number')
                    ->label('No. Kad Pengenalan')
                    ->placeholder('No. Kad Pengenalan')
                    ->required(),

                    TextInput::make('name')
                    ->label('Nama')
                    ->placeholder('Nama')
                    ->required(),

                    TextInput::make('age')
                    ->label('Umur')
                    ->placeholder('Umur')
                    ->required(),


                    TextInput::make('email')
                    ->label('E-mel')
                    ->placeholder('E-mel'),

                    TextInput::make('phone_number')
                    ->label('No. Telefon')
                    ->placeholder('No. Telefon')
                    ->required(),

                    TextInput::make('home_phone')
                    ->label('No. Telefon Rumah')
                    ->placeholder('No. Telefon Rumah')
                    ->required(),

                    TextInput::make('address')
                    ->label('Alamat')
                    ->placeholder('Alamat')
                    ->required(),

                    Select::make('residence_status')
                    ->options([
                        'kekal' => 'kekal',
                        'Sewa' => 'Sewa',
                    ])
                    ->native(false),

                    TextInput::make('password')
                    ->label('Kata Laluan')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->placeholder(fn ($record) => $record ? '••••••••' : 'Kata Laluan')
                    ->helperText(fn ($record) => $record ? 'Leave blank to keep current password' : 'Default will be their IC number if left blank')
                    ->revealable(),
                    
                    


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
              // Add No_Ahli to your table columns 
              TextColumn::make('No_Ahli')
              ->label('No. Ahli')
              ->searchable()
              ->sortable(),
              
          TextColumn::make('name')
              ->label('Nama')
              ->searchable()
              ->sortable(),
              
          TextColumn::make('ic_number')
              ->label('No. Kad Pengenalan')
              ->searchable(),
              
          TextColumn::make('phone_number')
              ->label('No. Telefon'),
              
          TextColumn::make('residence_status')
              ->label('Status Kediaman'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
