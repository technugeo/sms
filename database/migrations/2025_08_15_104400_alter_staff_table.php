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
            // Drop old enum column
            $table->dropColumn('access_level');

            // Add new role_id column referencing spatie/roles table
            $table->string('role')
                ->after('nationality_type');

            $table->enum('employment_status', ['Active', 'Inactive', 'Terminated','Deactivate'])
                ->default('Active')
                ->after('role');

            $table->enum('staff_type', array_column(\App\Enum\StaffTypeEnum::cases(), 'value'))
                ->default('full-time')
                ->after('employment_status');

            $table->softDeletes()->after('staff_type');

            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->integer('department_id')->nullable()->change();
            
            $table->unsignedBigInteger('faculty_id')->nullable()
                ->after('department_id');

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
            // Drop new FK column
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');

            // Restore old enum (using your RoleEnum)
            $table->enum('access_level', array_column(\App\Enum\RoleEnum::cases(), 'value'))
                ->after('nationality_type');

            $table->integer('department_id')->nullable()->change();
            $table->integer('created_by')->change();
            $table->integer('updated_by')->change();
            $table->integer('deleted_by')->nullable()->change();

            $table->dropColumn('employment_status');
            $table->dropColumn('staff_type');
            $table->dropSoftDeletes();
        });
    }
};
