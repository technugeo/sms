<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lib_course_prog', function (Blueprint $table) {
            // Change created_by, updated_by, deleted_by from integer to varchar(200)
            $table->string('created_by', 200)->nullable()->change();
            $table->string('updated_by', 200)->nullable()->change();
            $table->string('deleted_by', 200)->nullable()->change(); // in case deleted_by can be null

            // Change prog_code from integer to varchar(20)
            $table->string('prog_code', 20)->change();

            // Make sponsoring_body nullable
            $table->string('sponsoring_body')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('lib_course_prog', function (Blueprint $table) {
            // Revert changes if needed
            $table->integer('created_by')->change();
            $table->integer('updated_by')->change();
            $table->integer('deleted_by')->nullable()->change();

            $table->integer('prog_code')->change();

            $table->string('sponsoring_body')->nullable(false)->change();
        });
    }
};
