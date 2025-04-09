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
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('dependent_id')->nullable();
            $table->foreign('dependent_id')->references('dependent_id')->on('dependents')->onDelete('cascade');
            $table->string('name');
            $table->string('member_id')->unique();
            $table->date('date_of_death');
            $table->text('cause_of_death')->nullable();
            $table->dateTime('date_of_record')->useCurrent();
            $table->text('funeral_details')->nullable();
            $table->string('contact_person');
            $table->string('contact_phone');
            $table->string('address')->nullable();
            $table->string('death_certificate_number')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'verified'])->default('pending');
            $table->json('attachments')->nullable();
            $table->string('location_of_death')->nullable();
            $table->timestamps();
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

