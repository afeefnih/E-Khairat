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
        Schema::table('death_records', function (Blueprint $table) {
            // Add polymorphic relationship columns
            $table->string('deceased_type')->nullable()->after('id');
            $table->unsignedBigInteger('deceased_id')->nullable()->after('deceased_type');

            // Make dependent_id nullable for backward compatibility
            $table->unsignedBigInteger('dependent_id')->nullable()->change();

            // Add index for the polymorphic relationship
            $table->index(['deceased_type', 'deceased_id']);
        });

        // Update existing records to use the polymorphic relationship
        DB::statement("UPDATE death_records SET deceased_type = 'App\\Models\\Dependent', deceased_id = dependent_id WHERE dependent_id IS NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('death_records', function (Blueprint $table) {
            $table->dropIndex(['deceased_type', 'deceased_id']);
            $table->dropColumn(['deceased_type', 'deceased_id']);
            $table->unsignedBigInteger('dependent_id')->nullable(false)->change();
        });
    }
};
