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
use Illuminate\Support\Facades\Log;

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
                ->url(function ($record) {
                    $url = DeathRecordResource::getUrl('create', [
                        'deceased_type' => 'App\\Models\\Dependent',
                        'deceased_id' => $record->dependent_id,
                    ]);
                    return $url;
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
