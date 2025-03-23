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
        Schema::create('payment_categories', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key (id)
            $table->string('category_name'); // Name of the payment category
            $table->text('category_description')->nullable(); // Optional description for the category
            $table->decimal('amount', 10, 2); // Amount for the payment category
            $table->enum('category_status', ['active', 'inactive'])->default('active'); // Active or inactive status
            $table->timestamps(); // Created and updated timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_categories');
    }
};
