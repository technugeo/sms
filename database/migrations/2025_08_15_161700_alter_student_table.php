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
        Schema::table('student', function (Blueprint $table) {
            $table->string('nric', 12)->nullable()->change(); 

            $table->softDeletes()->after('updated_at'); // Adds deleted_at column
            $table->unsignedBigInteger('address_id')->nullable()->after('nationality');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student', function (Blueprint $table) {
            $table->dropUnique('student_nric_unique');
            $table->string('nric', 12)->unique()->change();

            $table->dropSoftDeletes(); // Removes deleted_at column
        });
    }
};
