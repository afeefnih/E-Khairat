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
        Schema::create('khairat_kematian_funds', function (Blueprint $table) {
            $table->id('fund_id'); // Primary key: fund_id
            $table->string('No_Ahli'); // Foreign key referencing No_Ahli from users
            $table->decimal('Payment_amount', 8, 2);
            $table->string('payment_status');
            $table->timestamp('payment_date');
            $table->string('Payment_For');
            $table->foreign('No_Ahli')->references('No_Ahli')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('khairat_kematian_funds');
    }
};
