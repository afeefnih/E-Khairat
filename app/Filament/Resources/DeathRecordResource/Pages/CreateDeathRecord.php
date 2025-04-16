<?php

namespace App\Filament\Resources\DeathRecordResource\Pages;

use App\Filament\Resources\DeathRecordResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\User;
use App\Models\Dependent;
use Filament\Notifications\Notification;

class CreateDeathRecord extends CreateRecord
{
    protected static string $resource = DeathRecordResource::class;

    protected function afterCreate(): void
    {
        // Get the created record
        $record = $this->record;

        // Handle status updates based on the death record type
        if ($record->deceased_type === User::class && $record->deceased_id) {
            // Logic for when a primary member has died
            // You might want to update the user status or trigger other processes

            // For example, you might want to set a deceased flag on the user
            $user = User::find($record->deceased_id);
            if ($user) {
                // You could add a 'status' column to users table and update it
                // $user->status = 'deceased';
                // $user->save();

                // Or you could handle dependents of this user
                // For example, notify admins about orphaned dependents
                $dependentsCount = $user->dependents()->count();
                if ($dependentsCount > 0) {
                    Notification::make()
                        ->title("Member {$user->name} has {$dependentsCount} dependents")
                        ->warning()
                        ->persistent()
                        ->send();
                }
            }
        } elseif ($record->deceased_type === Dependent::class && $record->deceased_id) {
            // Logic for when a dependent has died
            $dependent = Dependent::find($record->deceased_id);
            if ($dependent) {
                // You might want to update the dependent status
                // $dependent->status = 'deceased';
                // $dependent->save();
            }
        }
    }
}
