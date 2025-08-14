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

        Schema::create('student_guardians', function (Blueprint $table) {
            $table->id();
            $table->string('matric_id');
            $table->enum('guardian_type', array_column(\App\Enum\GuardianEnum::cases(), 'value'));
            $table->string('full_name');
            $table->string('ic_passport_no');
            $table->string('nationality');
            $table->string('address')->nullable();
            $table->string('phone_hp');
            $table->string('phone_house')->nullable();
            $table->string('phone_office')->nullable();
            $table->string('email');
            $table->string('occupation')->nullable();
            $table->integer('monthly_income');
            $table->enum('is_emergency_contact', ['yes', 'no'])->default('no');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_guardians');
    }
};
