<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Make sure the admin role exists
        $adminRole = Role::where('name', 'admin')->first();

        if (!$adminRole) {
            $adminRole = Role::create([
                'name' => 'admin',
                'description' => 'Administrator'
            ]);
        }

        // Create admin user if it doesn't exist
        $adminUser = User::where('email', 'admin@example.com')->first();

        if (!$adminUser) {
            $adminUser = User::create([
                'No_Ahli' => 'ADM-0001',
                'ic_number' => '000000000000',
                'name' => 'System Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'), // You should change this in production
                'phone_number' => '0123456789',
                'address' => 'Admin Office',
                'age' => 30,
                'home_phone' => '03-12345678',
                'residence_status' => 'kekal',
                'remember_token' => null,
            ]);
        }

        // Attach the admin role to the user (if not already attached)
        if (!$adminUser->hasRole('admin')) {
            $adminUser->roles()->attach($adminRole->id);
        }

        $this->command->info('Admin user created successfully!');
    }
}
