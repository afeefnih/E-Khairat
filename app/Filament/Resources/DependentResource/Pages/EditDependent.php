<?php

namespace App\Filament\Resources\DependentResource\Pages;

use App\Filament\Resources\DependentResource;
use App\Filament\Resources\DeathRecordResource;
use App\Filament\Resources\UserResource;
use App\Models\DeathRecord;
use App\Models\Dependent;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class EditDependent extends EditRecord
{
    protected static string $resource = DependentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('recordDeath')
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
                    Forms\Components\TimePicker::make('time_of_death')->label('Masa Kematian')->seconds(false),
                    Forms\Components\TextInput::make('place_of_death')
                        ->label('Tempat Kematian')
                        ->required()
                        ->validationMessages([
                            'required' => 'Tempat kematian diperlukan.',
                        ]),
                    Forms\Components\Textarea::make('cause_of_death')->label('Sebab Kematian')->rows(3),
                    Forms\Components\Textarea::make('death_notes')->label('Catatan')->rows(3),
                    Forms\Components\FileUpload::make('death_attachment_path')
                        ->label('Sijil Kematian')
                        ->directory('death-certificates')
                        ->visibility('private')
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                        ->maxSize(5120), // 5MB
                ])
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

                        // Add member number if available
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

            Actions\Action::make('viewDeathRecord')
                ->label('Lihat Maklumat Kematian')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->visible(fn($record) => $record && $record->isDeceased())
                ->url(function ($record) {
                    // Try to find the death record through polymorphic relationship
                    $deathRecord = DeathRecord::where('deceased_type', 'App\\Models\\Dependent')
                                           ->where('deceased_id', $record->dependent_id)
                                           ->first();

                    if ($deathRecord) {
                        return DeathRecordResource::getUrl('edit', ['record' => $deathRecord->id]);
                    }

                    return null;
                }),

            Actions\Action::make('view_member')
                ->label('Lihat Ahli')
                ->icon('heroicon-o-user')
                ->color('success')
                ->visible(fn (Dependent $record) => $record->user_id !== null)
                ->url(function (Dependent $record) {
                    // Link to the related User/Member resource page
                    if ($record->user_id) {
                        return UserResource::getUrl('edit', ['record' => $record->user_id]);
                    }
                    return null;
                }),

            Actions\DeleteAction::make()->label('Padam'),
        ];
    }
}
