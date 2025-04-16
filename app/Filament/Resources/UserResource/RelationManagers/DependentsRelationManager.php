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
use App\Models\DeathRecord;
use App\Filament\Resources\DeathRecordResource;
use Illuminate\Support\Facades\DB;

class DependentsRelationManager extends RelationManager
{
    protected static string $relationship = 'dependents';

    protected static ?string $recordTitleAttribute = 'full_name';

    public function form(Form $form): Form
    {
        return $form->schema([
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
                ->unique(Dependent::class, 'ic_number', fn($record) => $record)
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
                Tables\Columns\TextColumn::make('full_name')->searchable()->sortable(),

                Tables\Columns\TextColumn::make('relationship')->sortable(),

                Tables\Columns\TextColumn::make('age')->numeric()->sortable(),

                Tables\Columns\TextColumn::make('ic_number')->searchable(),

                // Add deceased status column
                Tables\Columns\IconColumn::make('deceased_status')->label('Deceased')->boolean()->getStateUsing(fn($record) => $record && $record->isDeceased())->trueIcon('heroicon-o-check-circle')->falseIcon('heroicon-o-x-circle')->trueColor('danger')->falseColor('success'),

                // Add date of death column when applicable
                Tables\Columns\TextColumn::make('death_date')
                    ->label('Date of Death')
                    ->getStateUsing(function ($record) {
                        if (!$record) {
                            return null;
                        }
                        $deathRecord = $record->deathRecord;
                        return $deathRecord ? $deathRecord->date_of_death : null;
                    })
                    ->date()
                    ->visible(fn($record) => $record && $record->isDeceased()),

                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('relationship')->options([
                    'Bapa' => 'Bapa',
                    'Ibu' => 'Ibu',
                    'Pasangan' => 'Pasangan',
                    'Anak' => 'Anak',
                ]),
                // Add filter for deceased status
                Tables\Filters\SelectFilter::make('deceased')
                    ->label('Death Status')
                    ->options([
                        '1' => 'Deceased',
                        '0' => 'Alive',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === null) {
                            return $query;
                        }

                        if ($data['value'] === '1') {
                            return $query->whereHas('deathRecord');
                        }

                        if ($data['value'] === '0') {
                            return $query->whereDoesntHave('deathRecord');
                        }
                    }),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()->successNotification(Notification::make()->success()->title('Dependent added')->body('The dependent has been added successfully.'))])
            ->actions([
                Tables\Actions\EditAction::make()->successNotification(Notification::make()->success()->title('Dependent updated')->body('The dependent has been updated successfully.')),
                Tables\Actions\DeleteAction::make()->successNotification(Notification::make()->success()->title('Dependent deleted')->body('The dependent has been deleted successfully.')),

                // In the recordDeath action inside DependentsRelationManager
                Tables\Actions\Action::make('recordDeath')
                    ->label('Record Death')
                    ->icon('heroicon-o-document-text')
                    ->color('danger')
                    ->visible(fn($record) => $record && !$record->isDeceased())
                    ->form([
                        Forms\Components\DatePicker::make('date_of_death')
                            ->required()
                            ->label('Date of Death')
                            ->validationMessages([
                                'required' => 'Tarikh kematian diperlukan.',
                            ]),
                        Forms\Components\TimePicker::make('time_of_death')->label('Time of Death')->seconds(false),
                        Forms\Components\TextInput::make('place_of_death')
                            ->label('Place of Death')
                            ->required()
                            ->validationMessages([
                                'required' => 'Tempat kematian diperlukan.',
                            ]),
                        Forms\Components\Textarea::make('cause_of_death')->label('Cause of Death')->rows(3),
                        Forms\Components\Textarea::make('death_notes')->label('Notes')->rows(3),
                        Forms\Components\FileUpload::make('death_attachment_path')
                            ->label('Death Certificate')
                            ->directory('death-certificates')
                            ->visibility('private')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->maxSize(5120), // 5MB
                    ])
                    // In your DependentsRelationManager.php
                    ->action(function (array $data, $record) {
                        // Begin a database transaction
                        DB::beginTransaction();

                        try {
                            // First check if a death record already exists for this dependent
                            $existingRecord = DeathRecord::where('dependent_id', $record->dependent_id)
                                ->orWhere(function ($query) use ($record) {
                                    $query->where('deceased_type', 'App\\Models\\Dependent')->where('deceased_id', $record->dependent_id);
                                })
                                ->first();

                            if ($existingRecord) {
                                throw new \Exception('A death record already exists for this dependent.');
                            }

                            // Create the death record
                            $deathRecord = new DeathRecord();
                            $deathRecord->deceased_type = 'App\\Models\\Dependent';
                            $deathRecord->deceased_id = $record->dependent_id;
                            $deathRecord->dependent_id = $record->dependent_id; // For backward compatibility
                            $deathRecord->date_of_death = $data['date_of_death'];
                            $deathRecord->time_of_death = $data['time_of_death'] ?? null;
                            $deathRecord->place_of_death = $data['place_of_death'];
                            $deathRecord->cause_of_death = $data['cause_of_death'] ?? null;
                            $deathRecord->death_notes = $data['death_notes'] ?? null;
                            $deathRecord->death_attachment_path = $data['death_attachment_path'] ?? null;
                            $deathRecord->save();

                            DB::commit();

                            Notification::make()->success()->title('Death Record Created')->body('The death record has been created successfully.')->send();
                        } catch (\Exception $e) {
                            DB::rollBack();

                            Notification::make()
                                ->danger()
                                ->title('Error')
                                ->body('Failed to create death record: ' . $e->getMessage())
                                ->send();
                        }
                    }),

                // View Death Record action - redirect to DeathRecordResource
                Tables\Actions\Action::make('viewDeathRecord')
                    ->label('View Death Details')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->visible(fn($record) => $record && $record->isDeceased())
                    ->url(function ($record) {
                        // Find the death record through the polymorphic relationship
                        $deathRecord = DeathRecord::where('deceased_type', 'App\\Models\\Dependent')->where('deceased_id', $record->dependent_id)->first();

                        if ($deathRecord) {
                            return DeathRecordResource::getUrl('edit', ['record' => $deathRecord->id]);
                        }

                        return null;
                    }),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }
}
