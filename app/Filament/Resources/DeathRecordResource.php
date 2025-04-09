<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeathRecordResource\Pages;
use App\Models\DeathRecord;
use App\Models\User;
use App\Models\Dependent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DeathRecordResource extends Resource
{
    protected static ?string $model = DeathRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Kematian Ahli';

    protected static ?string $navigationLabel = 'Rekod Kematian';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Select User or Dependent
            Forms\Components\Select::make('user_id')
                ->label('Ahli')
                ->relationship('user', 'name')
                ->searchable()
                ->preload()
                ->required(fn () => !request()->has('data.dependent_id')) // Required if no dependent selected
                ->disabled(fn () => request()->has('data.dependent_id')),

            Forms\Components\Select::make('dependent_id')
                ->label('Tanggungan')
                ->relationship('dependent', 'full_name')
                ->searchable()
                ->preload()
                ->required(fn () => !request()->has('data.user_id')) // Required if no user selected
                ->disabled(fn () => request()->has('data.user_id')),

            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->validationMessages([
                    'required' => 'Nama diperlukan.',
                    'max' => 'Nama tidak boleh melebihi 255 aksara.',
                ]),

            Forms\Components\DatePicker::make('date_of_death')
                ->required()
                ->label('Tarikh Kematian')
                ->validationMessages([
                    'required' => 'Tarikh kematian diperlukan.',
                ]),

            Forms\Components\TextInput::make('cause_of_death')
                ->nullable()
                ->maxLength(255)
                ->label('Punca Kematian'),

            Forms\Components\DateTimePicker::make('date_of_record')
                ->default(now())
                ->required()
                ->label('Tarikh Rekod'),

            Forms\Components\TextInput::make('funeral_details')
                ->nullable()
                ->maxLength(255)
                ->label('Maklumat Pengkebumian'),

            Forms\Components\TextInput::make('contact_person')
                ->required()
                ->maxLength(255)
                ->label('Nama Orang Yang Dihubungi'),

            Forms\Components\TextInput::make('contact_phone')
                ->required()
                ->maxLength(255)
                ->label('Nombor Telefon Orang Yang Dihubungi'),

            Forms\Components\TextInput::make('address')
                ->nullable()
                ->maxLength(255)
                ->label('Alamat'),

            Forms\Components\TextInput::make('death_certificate_number')
                ->nullable()
                ->maxLength(255)
                ->label('Nombor Sijil Kematian'),

            Forms\Components\Textarea::make('notes')
                ->nullable()
                ->label('Catatan'),

            Forms\Components\Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'confirmed' => 'Disahkan',
                    'verified' => 'Disahkan',
                ])
                ->default('pending')
                ->label('Status Rekod'),

            Forms\Components\FileUpload::make('attachments')
                ->multiple()
                ->label('Lampiran')
                ->image(),

            Forms\Components\TextInput::make('location_of_death')
                ->nullable()
                ->maxLength(255)
                ->label('Lokasi Kematian'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Ahli')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('dependent.full_name')
                    ->label('Nama Tanggungan')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_of_death')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cause_of_death')
                    ->label('Punca Kematian')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status Rekod'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Ahli')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('dependent_id')
                    ->label('Tanggungan')
                    ->relationship('dependent', 'full_name')
                    ->searchable()
                    ->preload(),
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
            // Define relationships if necessary
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeathRecords::route('/'),
            'create' => Pages\CreateDeathRecord::route('/create'),
            'edit' => Pages\EditDeathRecord::route('/{record}/edit'),
        ];
    }
}
