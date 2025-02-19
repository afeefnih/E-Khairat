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
        // Create collection_funds table
Schema::create('collection_funds', function (Blueprint $table) {
    $table->id('Collection_Id'); // Primary key: Collection_Id
    $table->string('Collection_Name');
    $table->decimal('Collection_amount', 8, 2);
    $table->timestamp('Collection_date');
    $table->string('No_Ahli'); // Foreign key referencing No_Ahli from users
    $table->foreign('No_Ahli')->references('No_Ahli')->on('users');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_funds');
    }
};
