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
       // Create dependents table
Schema::create('dependents', function (Blueprint $table) {
    $table->id('dependent_id'); // Primary key: dependent_id
    $table->string('No_Ahli'); // Foreign key referencing No_Ahli from users
    $table->string('full_name');
    $table->string('relationship');
    $table->integer('age');
    $table->string('ic_number');
    $table->foreign('No_Ahli')->references('No_Ahli')->on('users')->onDelete('cascade'); // Foreign Key Constraint
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dependent');
    }
};
