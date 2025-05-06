<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('death_records', function (Blueprint $table) {
            $table->string('non_member_name')->nullable();
            $table->string('non_member_ic_number')->nullable();
            $table->integer('non_member_age')->nullable();
            $table->string('non_member_relationship')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('death_records', function (Blueprint $table) {
            $table->dropColumn('non_member_name');
            $table->dropColumn('non_member_ic_number');
            $table->dropColumn('non_member_age');
            $table->dropColumn('non_member_relationship');
        });
    }
};
