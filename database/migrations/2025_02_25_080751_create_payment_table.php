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
        Schema::create('payments', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key (payment_id)
            $table->unsignedBigInteger('user_id'); // Foreign key to users table (users.id)
            $table->unsignedBigInteger('payment_category_id'); // Foreign key to payment_categories table (payment_categories.id)
            $table->decimal('amount', 10, 2); // The payment amount
            $table->string('status_id'); // Payment status (success, pending, failed)
            $table->string('billcode')->unique(); // Unique billcode from the payment gateway
            $table->string('order_id')->nullable(); // External order reference (if applicable)
            $table->timestamp('paid_at')->nullable(); // Timestamp for when the payment was completed
            $table->timestamps(); // Created and updated timestamps

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('payment_category_id')->references('id')->on('payment_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment');
    }
};
