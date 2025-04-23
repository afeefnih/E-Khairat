<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTableNullableColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Make 'age' nullable
            $table->integer('age')->nullable()->change();

            // Make 'address' nullable
            $table->string('address')->nullable()->change();

            // Make 'home_phone' nullable
            $table->string('home_phone')->nullable()->change();

            // Make 'residence_status' nullable
            $table->string('residence_status')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert 'age' to not nullable if needed
            $table->integer('age')->nullable(false)->change();

            // Revert 'address' to not nullable if needed
            $table->string('address')->nullable(false)->change();

            // Revert 'home_phone' to not nullable if needed
            $table->string('home_phone')->nullable(false)->change();

            // Revert 'residence_status' to not nullable if needed
            $table->string('residence_status')->nullable(false)->change();
        });
    }
}
