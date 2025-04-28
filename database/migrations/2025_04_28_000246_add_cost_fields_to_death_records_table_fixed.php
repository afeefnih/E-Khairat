<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add columns only if they don't exist
        if (!Schema::hasColumn('death_records', 'custom_amount')) {
            Schema::table('death_records', function (Blueprint $table) {
                $table->decimal('custom_amount', 10, 2)->nullable()->after('death_attachment_path');
            });
        }

        if (!Schema::hasColumn('death_records', 'custom_amount_notes')) {
            Schema::table('death_records', function (Blueprint $table) {
                $table->text('custom_amount_notes')->nullable()->after('custom_amount');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop columns only if they exist
        if (Schema::hasColumn('death_records', 'custom_amount_notes')) {
            Schema::table('death_records', function (Blueprint $table) {
                $table->dropColumn('custom_amount_notes');
            });
        }

        if (Schema::hasColumn('death_records', 'custom_amount')) {
            Schema::table('death_records', function (Blueprint $table) {
                $table->dropColumn('custom_amount');
            });
        }
    }
};
