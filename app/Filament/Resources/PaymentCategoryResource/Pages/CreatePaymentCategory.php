<?php

namespace App\Filament\Resources\PaymentCategoryResource\Pages;

use App\Filament\Resources\PaymentCategoryResource;
use App\Models\Payment;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use App\Notifications\NewPaymentCategoryNotification;
use Illuminate\Support\Facades\Bus;

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

        // ======== PART 1: CREATE PAYMENT RECORDS FOR ALL USERS ========
        // Get IDs of ALL non-admin users (no limit)
        $nonAdminUserIds = User::whereDoesntHave('roles', function ($query) {
                $query->where('name', 'admin');
            })
            ->pluck('id')
            ->toArray();

        // Prepare batch insert data for ALL users (payment records for everyone)
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
        // THIS CREATES PAYMENT RECORDS FOR ALL USERS
        $chunkSize = 50; // Adjust based on your database capabilities
        foreach (array_chunk($paymentRecords, $chunkSize) as $chunk) {
            Payment::insert($chunk);
        }

        // ======== PART 2: SEND NOTIFICATIONS TO ONLY 5 USERS ========
        // Get ONLY 5 non-admin users for notifications (development limit)
        $nonAdminUsers = User::whereDoesntHave('roles', function ($query) {
                $query->where('name', 'admin');
            })
            ->take(5) // Limit to ONLY 5 users for notifications during development
            ->get();

        // Send notifications to ONLY those 5 limited users via queue
        foreach ($nonAdminUsers as $user) {
            $user->notify(new NewPaymentCategoryNotification($paymentCategory));
        }

        // Total number of payment records created vs number of notifications sent
        $totalPayments = count($nonAdminUserIds);
        $totalNotifications = count($nonAdminUsers);

        // Show admin notification about successful notification dispatch
        Notification::make()
            ->title('Kutipan sumbangan berjaya ditambah')
            ->body("Rekod pembayaran dicipta untuk {$totalPayments} ahli. Notifikasi dihantar kepada {$totalNotifications} ahli sahaja (had pembangunan).")
            ->success()
            ->send();
    }
}
