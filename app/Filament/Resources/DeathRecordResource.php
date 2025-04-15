<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeathRecordResource\Pages;
use App\Models\DeathRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class DeathRecordResource extends Resource
{
    protected static ?string $model = DeathRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Death Records';

    protected static ?string $navigationGroup = 'Records Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('dependent_id')
                    ->relationship('dependent', 'full_name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\DatePicker::make('date_of_death')
                    ->required()
                    ->label('Date of Death'),

                Forms\Components\TimePicker::make('time_of_death')
                    ->label('Time of Death')
                    ->seconds(false),

                Forms\Components\TextInput::make('place_of_death')
                    ->label('Place of Death')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('cause_of_death')
                    ->label('Cause of Death')
                    ->rows(3)
                    ->maxLength(1000),

                Forms\Components\Textarea::make('death_notes')
                    ->label('Notes')
                    ->rows(3)
                    ->maxLength(1000),

                Forms\Components\Section::make('Death Certificate')
                    ->schema([
                        Forms\Components\FileUpload::make('death_attachment_path')
                            ->label('Death Certificate')
                            ->directory('death-certificates')
                            ->visibility('private')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->downloadable()
                            ->maxSize(5120), // 5MB
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('dependent.full_name')
                    ->searchable()
                    ->sortable()
                    ->label('Dependent Name'),

                Tables\Columns\TextColumn::make('dependent.ic_number')
                    ->searchable()
                    ->label('IC Number'),

                Tables\Columns\TextColumn::make('date_of_death')
                    ->date()
                    ->sortable()
                    ->label('Date of Death'),

                Tables\Columns\TextColumn::make('place_of_death')
                    ->searchable()
                    ->limit(30)
                    ->label('Place of Death'),

                Tables\Columns\IconColumn::make('death_attachment_path')
                    ->boolean()
                    ->label('Certificate')
                    ->trueIcon('heroicon-o-document')
                    ->falseIcon('heroicon-o-x-mark')
                    ->getStateUsing(fn (DeathRecord $record) => $record->death_attachment_path !== null),

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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('viewCertificate')
                    ->label('View Certificate')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->visible(fn ($record) => $record->death_attachment_path)
                    ->url(fn ($record) => $record->death_attachment_path ? Storage::url($record->death_attachment_path) : null, true),
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
            'index' => Pages\ListDeathRecords::route('/'),
            'create' => Pages\CreateDeathRecord::route('/create'),
            'edit' => Pages\EditDeathRecord::route('/{record}/edit'),
        ];
    }
}
