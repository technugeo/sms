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

        Schema::create('student_eduhistory', function (Blueprint $table) {
            $table->id();
            $table->string('matric_id');
            $table->string('institution_name');
            $table->string('country');
            $table->string('level');
            $table->string('subject_name')->nullable();
            $table->string('grade')->nullable();
            $table->string('programme_name')->nullable();
            $table->decimal('cgpa', 3, 2)->nullable();
            $table->smallInteger('start_year')->nullable();
            $table->smallInteger('end_year')->nullable();

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_eduhistory');
    }
};
