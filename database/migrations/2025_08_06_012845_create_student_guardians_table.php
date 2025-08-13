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
            $table->string('guardian_type')->nullable();
            $table->string('full_name');
            $table->string('ic_passport_no');
            $table->string('nationality');
            $table->string('address');
            $table->string('phone_hp');
            $table->string('phone_house');
            $table->string('phone_office');
            $table->string('email');
            $table->string('occupation');
            $table->integer('monthly_income');

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
        Schema::dropIfExists('student_guardians');
    }
};
