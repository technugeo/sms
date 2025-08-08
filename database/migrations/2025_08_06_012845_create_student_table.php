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

        Schema::create('student', function (Blueprint $table) {
            $table->id();
            $table->string('matric_id');
            $table->string('nric', 12)->unique();
            $table->string('passport_no')->nullable();
            $table->integer('phone_number');
            $table->string('full_name');
            $table->enum('nationality_type',array_column(\App\Enum\NationalityEnum::cases(), 'value'));
            $table->enum('citizen',array_column(\App\Enum\CitizenEnum::cases(), 'value'));
            $table->enum('marriage_status', array_column(\App\Enum\MarriageEnum::cases(), 'value'));
            $table->string('nationality');
            $table->enum('academic_status', array_column(\App\Enum\AcademicEnum::cases(), 'value'));
            $table->enum('gender', array_column(\App\Enum\GenderEnum::cases(), 'value'));
            $table->enum('race', array_column(\App\Enum\RaceEnum::cases(), 'value'));
            $table->enum('religion', array_column(\App\Enum\ReligionEnum::cases(), 'value'));
            $table->string('intake_month');
            $table->integer('intake_year');
            $table->integer('current_course');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student');
    }
};
