<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Temporarily disable foreign key checks for truncation
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear dependent tables first to avoid foreign key issues
        \DB::table('dependents')->truncate();
        \DB::table('role_user')->truncate();

        // Now run the seeders in proper order
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            PaymentCategorySeeder::class,
            PaymentSeeder::class,
            AdminSeeder::class,
        ]);

        // Enable foreign key checks again
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
