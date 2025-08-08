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

        Schema::create('lib_course_prog', function (Blueprint $table) {
            $table->id();
            $table->integer('prog_code');
            $table->string('prog_name');
            $table->integer('faculty_id');
            $table->string('programme_type')->nullable();
            $table->string('sponsoring_body');
            $table->string('status')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->integer('deleted_by');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lib_course_prog');
    }
};
