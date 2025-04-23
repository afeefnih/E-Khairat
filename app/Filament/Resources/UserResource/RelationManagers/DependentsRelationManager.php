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
use App\Filament\Resources\DependentResource;
use Illuminate\Support\Facades\DB;

class DependentsRelationManager extends RelationManager
{
    protected static string $relationship = 'dependents';

    protected static ?string $recordTitleAttribute = 'full_name';

    // Translate the title
    protected static ?string $title = 'Tanggungan';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('full_name')
                ->label('Nama Penuh')
                ->required()
                ->maxLength(255)
                ->validationMessages([
                    'required' => 'Nama penuh diperlukan.',
                    'max' => 'Nama penuh tidak boleh melebihi 255 aksara.',
                ]),

            Forms\Components\Select::make('relationship')
                ->label('Hubungan')
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
                ->label('Umur')
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
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nama Penuh')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => DependentResource::getUrl('edit', ['record' => $record->dependent_id]), false),

                Tables\Columns\TextColumn::make('relationship')
                    ->label('Hubungan')
                    ->sortable(),

                Tables\Columns\TextColumn::make('age')
                    ->label('Umur')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ic_number')
                    ->label('Nombor IC')
                    ->searchable(),

                // Add deceased status column
                Tables\Columns\IconColumn::make('deceased_status')
                    ->label('Status Kematian')
                    ->boolean()
                    ->getStateUsing(fn($record) => $record && $record->isDeceased())
                    ->trueIcon('heroicon-s-x-circle')  // Solid X circle for deceased
                    ->falseIcon('heroicon-s-check-circle')  // Solid check circle for alive
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->tooltip(fn($record) => $record && $record->isDeceased() ? 'Meninggal Dunia' : 'Masih Hidup'),

                // Add date of death column when applicable
                Tables\Columns\TextColumn::make('death_date')
                    ->label('Tarikh Kematian')
                    ->getStateUsing(function ($record) {
                        if (!$record) {
                            return null;
                        }
                        $deathRecord = $record->deathRecord;
                        return $deathRecord ? $deathRecord->date_of_death : null;
                    })
                    ->date()
                    ->visible(fn($record) => $record && $record->isDeceased()),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarikh Cipta')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('relationship')
                    ->label('Hubungan')
                    ->options([
                        'Bapa' => 'Bapa',
                        'Ibu' => 'Ibu',
                        'Pasangan' => 'Pasangan',
                        'Anak' => 'Anak',
                    ]),
                // Add filter for deceased status
                Tables\Filters\SelectFilter::make('deceased')
                    ->label('Status Kematian')
                    ->options([
                        '1' => 'Meninggal Dunia',
                        '0' => 'Masih Hidup',
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
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Tanggungan')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Tanggungan Ditambah')
                            ->body('Tanggungan telah berjaya ditambah.')
                    )
            ])
            ->actions([
                // Modified EditAction to redirect to DependentResource instead of showing a popup
                Tables\Actions\Action::make('edit')
                    ->icon('heroicon-o-pencil')
                    ->color('primary')
                    ->url(fn ($record) => DependentResource::getUrl('edit', ['record' => $record->dependent_id]))
                    ->openUrlInNewTab(false),

                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Tanggungan Dipadam')
                            ->body('Tanggungan telah berjaya dipadam.')
                    ),

                // In the recordDeath action inside DependentsRelationManager
                Tables\Actions\Action::make('recordDeath')
                    ->label('Rekod Kematian')
                    ->icon('heroicon-o-document-text')
                    ->color('danger')
                    ->visible(fn($record) => $record && !$record->isDeceased())
                    ->form([
                        Forms\Components\DatePicker::make('date_of_death')
                            ->required()
                            ->label('Tarikh Kematian')
                            ->validationMessages([
                                'required' => 'Tarikh kematian diperlukan.',
                            ]),
                        Forms\Components\TimePicker::make('time_of_death')
                            ->label('Masa Kematian')
                            ->seconds(false),
                        Forms\Components\TextInput::make('place_of_death')
                            ->label('Tempat Kematian')
                            ->required()
                            ->validationMessages([
                                'required' => 'Tempat kematian diperlukan.',
                            ]),
                        Forms\Components\Textarea::make('cause_of_death')
                            ->label('Sebab Kematian')
                            ->rows(3),
                        Forms\Components\Textarea::make('death_notes')
                            ->label('Catatan')
                            ->rows(3),
                        Forms\Components\FileUpload::make('death_attachment_path')
                            ->label('Sijil Kematian')
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
                            $existingRecord = DeathRecord::where(function ($query) use ($record) {
                                $query->where('deceased_type', 'App\\Models\\Dependent')
                                      ->where('deceased_id', $record->dependent_id);
                            })->first();

                            if ($existingRecord) {
                                throw new \Exception('Rekod kematian sudah wujud untuk tanggungan ini.');
                            }

                            // Create the death record using polymorphic relationship
                            $deathRecord = new DeathRecord();
                            $deathRecord->deceased_type = 'App\\Models\\Dependent';
                            $deathRecord->deceased_id = $record->dependent_id; // Use the dependent's ID
                            $deathRecord->date_of_death = $data['date_of_death'];
                            $deathRecord->time_of_death = $data['time_of_death'] ?? null;
                            $deathRecord->place_of_death = $data['place_of_death'];
                            $deathRecord->cause_of_death = $data['cause_of_death'] ?? null;
                            $deathRecord->death_notes = $data['death_notes'] ?? null;
                            $deathRecord->death_attachment_path = $data['death_attachment_path'] ?? null;

                            // Add member number for easier reference
                            if ($record->user && $record->user->No_Ahli) {
                                $deathRecord->member_no = $record->user->No_Ahli;
                            }

                            $deathRecord->save();

                            DB::commit();

                            Notification::make()
                                ->success()
                                ->title('Rekod Kematian Dibuat')
                                ->body('Rekod kematian telah berjaya dibuat.')
                                ->send();
                        } catch (\Exception $e) {
                            DB::rollBack();

                            Notification::make()
                                ->danger()
                                ->title('Ralat')
                                ->body('Gagal membuat rekod kematian: ' . $e->getMessage())
                                ->send();
                        }
                    }),

                // View Death Record action - redirect to DeathRecordResource
                Tables\Actions\Action::make('viewDeathRecord')
                    ->label('Lihat Maklumat Kematian')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->visible(fn($record) => $record && $record->isDeceased())
                    ->url(function ($record) {
                        // Find the death record through the polymorphic relationship only
                        $deathRecord = DeathRecord::where('deceased_type', 'App\\Models\\Dependent')
                            ->where('deceased_id', $record->dependent_id)
                            ->first();

                        if ($deathRecord) {
                            return DeathRecordResource::getUrl('edit', ['record' => $deathRecord->id]);
                        }

                        return null;
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Padam Terpilih')
                ])
            ]);
    }
}
