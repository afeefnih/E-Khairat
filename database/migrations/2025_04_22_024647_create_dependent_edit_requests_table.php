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
        Schema::create('dependent_edit_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Make dependent_id nullable and add the foreign key constraint
            $table->unsignedBigInteger('dependent_id')->nullable();
            $table->foreign('dependent_id')->references('dependent_id')->on('dependents')->onDelete('cascade');

            // Add request_type field to distinguish between edit and add requests
            $table->enum('request_type', ['edit', 'add','delete'])->default('edit');

            $table->string('full_name');
            $table->string('relationship');
            $table->integer('age');
            $table->string('ic_number', 12);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_comments')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dependent_edit_requests');
    }
};
