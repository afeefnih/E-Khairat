<?php

namespace App\Filament\Resources\PaymentCategoryResource\Pages;

use App\Filament\Resources\PaymentCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

class EditPaymentCategory extends EditRecord
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

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->disabled(fn ($record) => $record->payments()->count() > 0),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Payment category updated')
            ->body('The payment category has been updated successfully.');
    }
}
