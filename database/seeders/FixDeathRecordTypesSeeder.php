<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixDeathRecordTypesSeeder extends Seeder
{
    public function run()
    {
        // Fix User types
        DB::table('death_records')
            ->where('deceased_type', 'AppModelsUser')
            ->update(['deceased_type' => 'App\\Models\\User']);

        // Fix Dependent types
        DB::table('death_records')
            ->where('deceased_type', 'AppModelsDependent')
            ->update(['deceased_type' => 'App\\Models\\Dependent']);

        // Also fix any other potential format issues
        DB::table('death_records')
            ->where('deceased_type', 'App/Models/User')
            ->update(['deceased_type' => 'App\\Models\\User']);

        DB::table('death_records')
            ->where('deceased_type', 'App/Models/Dependent')
            ->update(['deceased_type' => 'App\\Models\\Dependent']);
    }
}
