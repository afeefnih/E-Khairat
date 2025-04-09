<?php

namespace App\Filament\Resources\DeathRecordResource\Pages;

use App\Filament\Resources\DeathRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeathRecord extends EditRecord
{
    protected static string $resource = DeathRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
