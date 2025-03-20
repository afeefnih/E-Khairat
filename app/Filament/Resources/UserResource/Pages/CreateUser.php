<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\User;
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
    $nextNumber = $maxNoAhli ? (intval($maxNoAhli) + 1) : 1;
    
    // Format with leading zeros (4 digits)
    $data['No_Ahli'] = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // If password is empty, use IC number as default password
        if (empty($data['password'])) {
            $data['password'] = Hash::make($data['ic_number']);
        }
        
        return $data;
    }
}
