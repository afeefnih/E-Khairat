<?php

namespace App\Filament\Resources\DependentEditRequestResource\Pages;

use App\Filament\Resources\DependentEditRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDependentEditRequest extends EditRecord
{
    protected static string $resource = DependentEditRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
