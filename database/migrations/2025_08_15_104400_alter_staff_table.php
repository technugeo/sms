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

            $table->softDeletes()->after('staff_type');

            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->string('created_by', 200)->nullable()->change();
            $table->string('updated_by', 200)->nullable()->change();
            $table->string('deleted_by', 200)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->integer('created_by')->change();
            $table->integer('updated_by')->change();
            $table->integer('deleted_by')->nullable()->change();
            
            $table->dropColumn('employment_status');
            $table->dropColumn('staff_type');
        });
    }
};
