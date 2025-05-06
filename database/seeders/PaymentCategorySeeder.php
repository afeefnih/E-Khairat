<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentCategory;

class PaymentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentCategory::create([
            'category_name' => 'Bayaran Pendaftaran',
            'category_description' => 'Pendaftaran menjadi ahli biro khairat kematian Masjid Taman Sutera',
            'amount' => 100,
            'category_status' => 'active',
        ]);
    }
}
