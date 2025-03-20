<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\PaymentCategory;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
{
    // User::factory(10)->create();

    PaymentCategory::create([
        'category_name' => 'Bayaran Pendaftran',
        'category_description' => 'Pendaftaran menjadi ahli biro khairat kematian Masjid Taman Sutera',
    ]);

    // Create admin user
    \App\Models\User::create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'), // Remember to change this in production
        'role' => 'admin',
        'No_Ahli' => 'ADMIN001',
        'age' => 30,
        'ic_number' => '000000000000', // Placeholder
        'home_phone' => '0123456789', // Placeholder
        'phone_number' => '0123456789', // Placeholder
        'address' => 'Admin Address', // Placeholder
        'residence_status' => 'kekal', // Placeholder

    ]);
}
}
