<?php

namespace App\Filament\Resources\DependentEditRequestResource\Pages;

use App\Filament\Resources\DependentEditRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDependentEditRequests extends ListRecords
{
    protected static string $resource = DependentEditRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // You can add actions here if needed
        ];
    }

    protected function getTableFiltersFormSchema(): array
    {
        // This ensures the 'pending' filter is applied by default
        return $this->getResource()::table($this->makeTable())
            ->getFiltersFormSchema()
            ->map(function ($filter) {
                if ($filter->getName() === 'status') {
                    $filter->default('pending');
                }
                return $filter;
            })
            ->toArray();
    }
}
