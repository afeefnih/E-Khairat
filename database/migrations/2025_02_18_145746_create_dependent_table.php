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
        $table->id('dependent_id'); // Primary key for the dependents table
        $table->unsignedBigInteger('user_id'); // Foreign key referencing the user's 'id' field
        $table->string('full_name');
        $table->string('relationship');
        $table->integer('age');
        $table->string('ic_number')->unique();
        $table->timestamps();

        // Foreign key constraint to reference the user_id field in the users table
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
