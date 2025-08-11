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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone_number');
            $table->string('nric', 12)->unique();
            $table->foreignId('nationality_id')->constrained('nationalities')->restrictOnDelete();
            $table->enum('nationality_type',array_column(\App\Enum\NationalityEnum::cases(), 'value'));
            $table->enum('citizen',array_column(\App\Enum\CitizenEnum::cases(), 'value'));
            $table->enum('marriage_status', array_column(\App\Enum\MarriageEnum::cases(), 'value'));
            $table->enum('gender', array_column(\App\Enum\GenderEnum::cases(), 'value'));
            $table->enum('race', array_column(\App\Enum\RaceEnum::cases(), 'value'));
            $table->enum('religion', array_column(\App\Enum\ReligionEnum::cases(), 'value'));
            $table->enum('access_level', array_column(\App\Enum\RoleEnum::cases(), 'value'));
            // $table->foreignId('address_id')->constrained('addresses')->restrictOnDelete();
            $table->foreignId('address_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('institute_id')->constrained('institute')->restrictOnDelete();
            $table->foreignId('department_id')->constrained('departments')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
