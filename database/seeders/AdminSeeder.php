<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        // First make sure we have the admin role
        $adminRole = Role::where('name', 'admin')->first();

        if (!$adminRole) {
            $adminRole = Role::create([
                'name' => 'admin',
                'description' => 'Administrator'
            ]);
        }

        // Create admin user
        $admin = User::create([
            'No_Ahli' => 'ADMIN001',
            'ic_number' => '000000000000',
            'name' => 'System Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), // You should change this to a secure password
            'phone_number' => '0123456789',
            'address' => 'Admin Office',
            'age' => 30,
            'home_phone' => '03-12345678',
            'residence_status' => 'Permanent',
        ]);

        // Assign admin role to the user
        $admin->roles()->attach($adminRole);

        $this->command->info('Admin user created successfully!');

    }
}
