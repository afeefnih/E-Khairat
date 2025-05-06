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
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

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
                ->url(function ($record) {
                    $url = DeathRecordResource::getUrl('create', [
                        'deceased_type' => 'App\\Models\\User',
                        'deceased_id' => $record->id,
                    ]);
                    return $url;
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
