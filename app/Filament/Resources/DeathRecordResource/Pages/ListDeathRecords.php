<?php

namespace App\Filament\Resources\DeathRecordResource\Pages;

use App\Filament\Resources\DeathRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeathRecords extends ListRecords
{
    protected static string $resource = DeathRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
