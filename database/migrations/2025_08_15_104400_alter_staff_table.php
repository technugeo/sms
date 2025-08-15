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
        Schema::table('staff', function (Blueprint $table) {
            $table->enum('employment_status', ['Active', 'Inactive', 'Terminated'])
                ->default('Active')
                ->after('access_level');

            $table->enum('staff_type', ['Full-time', 'Contract', 'Intern'])
                ->default('full-time')
                ->after('employment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn('employment_status');
            $table->dropColumn('staff_type');
        });
    }
};
