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
        Schema::create('death_records', function (Blueprint $table) {
            $table->id();

            // Link to the deceased person (User OR Dependent)
            $table->foreignId('user_id')
                  ->nullable()
                  ->unique()
                  ->constrained('users')
                  ->onDelete('set null'); // Keep record if user deleted

            // For dependent_id, use unsignedBigInteger to avoid Laravel's automatic naming conventions
            $table->unsignedBigInteger('dependent_id')->nullable();
            $table->foreign('dependent_id')
                  ->references('dependent_id')
                  ->unique()
                  ->on('dependents')
                  ->onDelete('set null');

            // Rest of your columns...
            $table->date('date_of_death')->nullable();
            $table->time('time_of_death')->nullable();
            $table->string('place_of_death')->nullable();
            $table->text('cause_of_death')->nullable();
            $table->text('death_notes')->nullable();
            $table->string('death_attachment_path')->nullable();

            $table->timestamps();
            $table->index(['user_id', 'dependent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('death_records');
    }
};
