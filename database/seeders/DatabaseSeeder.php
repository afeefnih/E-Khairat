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

    $this->call([
        RoleSeeder::class,
        AdminSeeder::class,
    ]);

}
}
