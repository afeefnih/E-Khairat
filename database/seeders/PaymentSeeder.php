<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Payment;
use App\Models\PaymentCategory;
use Illuminate\Support\Str;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the registration payment category
        $category = PaymentCategory::where('category_name', 'Bayaran Pendaftaran')->first();
        if (!$category) return;

        // Find all non-admin users
        $users = User::whereDoesntHave('roles', function ($q) {
            $q->where('name', 'admin');
        })->get();

        foreach ($users as $user) {
            // Avoid duplicate payments
            if (!Payment::where('user_id', $user->id)
                  ->where('payment_category_id', $category->id)
                  ->exists()) {

                // Create a payment for each user
                Payment::create([
                    'user_id' => $user->id,
                    'payment_category_id' => $category->id,
                    'amount' => $category->amount,
                    'status_id' => 1, // Mark as paid
                    'paid_at' => $user->registration_date, // Set paid_at to match user registration date
                    'billcode' => 'BILL-' . time() . '-' . Str::random(6),
                    'order_id' => 'ORD-' . date('Ymd') . '-' . Str::random(6),
                ]);
            }
        }
    }
}
