<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Resources\DeathRecordResource;
use App\Models\DeathRecord;
use App\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('recordDeath')
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
                ->action(function (array $data, $record) {
                    // Begin a database transaction
                    DB::beginTransaction();

                    try {
                        // First check if a death record already exists for this user
                        $existingRecord = DeathRecord::where(function ($query) use ($record) {
                            $query->where('deceased_type', 'App\\Models\\User')
                                  ->where('deceased_id', $record->id);
                        })->first();

                        if ($existingRecord) {
                            throw new \Exception('A death record already exists for this member.');
                        }

                        // Create the death record using polymorphic relationship
                        $deathRecord = new DeathRecord();
                        $deathRecord->deceased_type = 'App\\Models\\User';
                        $deathRecord->deceased_id = $record->id; // Use the user's ID
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

            Actions\Action::make('viewDeathRecord')
                ->label('View Death Details')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->visible(fn($record) => $record && $record->isDeceased())
                ->url(function ($record) {
                    // Try to find the death record through polymorphic relationship
                    $deathRecord = DeathRecord::where('deceased_type', 'App\\Models\\User')
                                           ->where('deceased_id', $record->id)
                                           ->first();

                    if ($deathRecord) {
                        return DeathRecordResource::getUrl('edit', ['record' => $deathRecord->id]);
                    }

                    return null;
                }),

            Actions\DeleteAction::make()
            ->hidden(fn($record) => $record->No_Ahli === 'ADM-0001'), // Hide delete for ADM-0001

        ];
    }
}
