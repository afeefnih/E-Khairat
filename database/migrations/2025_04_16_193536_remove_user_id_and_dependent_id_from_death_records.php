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
            // First drop the foreign key constraints
            $table->dropForeign(['user_id']);
            $table->dropForeign(['dependent_id']);

            // Then drop the index
            $table->dropIndex(['user_id', 'dependent_id']);

            // Finally drop the columns
            $table->dropColumn(['user_id', 'dependent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('death_records', function (Blueprint $table) {
            // Re-add the columns
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');

            $table->unsignedBigInteger('dependent_id')->nullable();
            $table->foreign('dependent_id')
                  ->references('dependent_id')
                  ->on('dependents')
                  ->onDelete('set null');

            $table->index(['user_id', 'dependent_id']);
        });
    }
};
