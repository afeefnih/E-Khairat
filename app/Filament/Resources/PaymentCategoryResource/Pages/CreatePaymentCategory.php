<?php

namespace App\Filament\Resources\PaymentCategoryResource\Pages;

use App\Filament\Resources\PaymentCategoryResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

class CreatePaymentCategory extends CreateRecord
{
    protected static string $resource = PaymentCategoryResource::class;

    // Add validation error notification
    protected function onValidationError(ValidationException $exception): void
    {
        Notification::make()
            ->title($exception->getMessage())
            ->danger()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
