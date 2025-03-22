<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\User;
use App\Models\Payment;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Get the last 'No_Ahli' and increment it
        $maxNoAhli = DB::table('users')
            ->whereNotNull('No_Ahli')
            ->where('No_Ahli', 'regexp', '^[0-9]+$') // Ensure we only get numeric values
            ->max('No_Ahli');

        // If no records exist or max is not found, start from 0
        $nextNumber = $maxNoAhli ? intval($maxNoAhli) + 1 : 0;

        // Format with leading zeros (4 digits)
        $data['No_Ahli'] = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // If password is empty, use IC number as default password
        if (empty($data['password'])) {
            $data['password'] = Hash::make($data['ic_number']);
        }

        return $data;
    }

    // In Filament 3.x, use this hook
    protected function afterCreate(): void
    {
        // Get the user that was just created
        $user = $this->record;

        // 1. Assign the 'user' role to the newly created user
        $userRole = Role::where('name', 'user')->first();
        if ($userRole) {
            // Check if the role is already assigned to prevent duplicates
            if (!$user->roles()->where('role_id', $userRole->id)->exists()) {
                $user->roles()->attach($userRole->id);
            }
        }
        // 2. Create a payment record for the user
        Payment::create([
            'user_id' => $user->id,
            'payment_category_id' => 1, // Adjust this as needed
            'amount' => 100, // Adjust this as needed
            'status_id' => 1, // Assuming 1 means paid
            'billcode' => 'ADMIN-' . $user->id, // Generate a billcode
            'order_id' => 'ORD-' . time() . '-' . $user->id, // Generate an order ID
            'request_title' => 'User Registration', // Set appropriate title
            'paid_at' => now(), // Current date and time
        ]);
    }
}
