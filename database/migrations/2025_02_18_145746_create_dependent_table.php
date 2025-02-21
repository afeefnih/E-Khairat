<?php

use App\Models\User;
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
        $table->string('No_Ahli'); // Foreign key: No_Ahli (string type to match the user's No_Ahli type)
        $table->foreign('No_Ahli')->references('No_Ahli')->on('users'); // Define foreign key relationship
        $table->string('full_name');
        $table->string('relationship');
        $table->integer('age');
        $table->string('ic_number');
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
