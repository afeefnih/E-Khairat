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

        // Get IDs of all non-admin users
        $nonAdminUserIds = User::whereDoesntHave('roles', function ($query) {
                $query->where('name', 'admin');
            })
            ->pluck('id')
            ->toArray();

        // Prepare batch insert data
        $paymentRecords = [];
        $now = now();

        foreach ($nonAdminUserIds as $userId) {
            $paymentRecords[] = [
                'user_id' => $userId,
                'payment_category_id' => $paymentCategory->id,
                'amount' => $paymentCategory->amount,
                'status_id' => 0, // Default unpaid status
                'billcode' => null,
                'order_id' => null,
                'paid_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Use chunk insert for better performance with large datasets
        $chunkSize = 50; // Adjust based on your database capabilities
        foreach (array_chunk($paymentRecords, $chunkSize) as $chunk) {
            Payment::insert($chunk);
        }
    }
}
