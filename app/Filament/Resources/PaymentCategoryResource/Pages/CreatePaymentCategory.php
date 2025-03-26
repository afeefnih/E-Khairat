<?php

namespace App\Filament\Resources\PaymentCategoryResource\Pages;

use App\Filament\Resources\PaymentCategoryResource;
use App\Models\Payment;
use App\Models\User;
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
    protected function afterCreate(): void
    {
        // Get the newly created payment category
        $paymentCategory = $this->record;

        // Get only non-admin users
        $nonAdminUsers = User::whereDoesntHave('roles', function ($query) {
                $query->where('name', 'admin');
            })
            ->get();

        // Create a payment record for each non-admin user
        foreach ($nonAdminUsers as $user) {
            Payment::create([
                'user_id' => $user->id,
                'payment_category_id' => $paymentCategory->id,
                'amount' => $paymentCategory->amount,
                'status_id' => 0, // Default unpaid status
                'billcode' => null,
                'order_id' => null,
                'paid_at' => null,
            ]);
        }
    }
}
