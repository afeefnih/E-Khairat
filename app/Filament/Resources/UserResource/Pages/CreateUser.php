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
        // Check if admin role is selected
        $isAdmin = false;

        if (isset($data['roles']) && !empty($data['roles'])) {
            $roleId = $data['roles'][0] ?? null; // Get the first (and only) selected role ID
            if ($roleId) {
                $role = \App\Models\Role::find($roleId);
                if ($role && $role->name === 'admin') {
                    $isAdmin = true;
                }
            }
        }

        if ($isAdmin) {
            // For admin users, use ADM- prefix
            $maxAdminNoAhli = \App\Models\User::whereNotNull('No_Ahli')
                ->where('No_Ahli', 'like', 'ADM-%')
                ->get()
                ->map(function ($user) {
                    return (int) substr($user->No_Ahli, 4);
                })
                ->max();

            $nextNumber = $maxAdminNoAhli ? $maxAdminNoAhli + 1 : 1;
            $data['No_Ahli'] = 'ADM-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        } else {
            // For regular users
            $maxNoAhli = \App\Models\User::whereNotNull('No_Ahli')
                ->where('No_Ahli', 'regexp', '^[0-9]+$')
                ->max('No_Ahli');

            $nextNumber = $maxNoAhli ? intval($maxNoAhli) + 1 : 1;
            $data['No_Ahli'] = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        }

        // If password is empty, use IC number as default password
        if (empty($data['password'])) {
            $data['password'] = Hash::make($data['ic_number']);
        }

        return $data;
    }

    // Update the afterCreate method
    protected function afterCreate(): void
    {
        // Get the user that was just created
        $user = $this->record;

        // No need to detach roles as we're using the relationship form field
        // which will properly set the role from the form data

        // Only create payment records for non-admin users
        if (!str_starts_with($user->No_Ahli, 'ADM-')) {
            \App\Models\Payment::create([
                'user_id' => $user->id,
                'payment_category_id' => 1,
                'amount' => 100,
                'status_id' => 1,
                'billcode' => 'BILL-' . time() . '-' . \Illuminate\Support\Str::random(6),
                'order_id' => 'ORD-' . date('Ymd') . '-' . \Illuminate\Support\Str::random(6),
                'request_title' => 'User Registration',
                'paid_at' => now(),
            ]);
        }

    }
}
