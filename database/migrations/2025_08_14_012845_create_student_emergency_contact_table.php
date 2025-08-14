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

        Schema::create('student_emergency_contact', function (Blueprint $table) {
            $table->id();
            $table->string('matric_id');
            $table->string('relationship')->nullable();
            $table->string('full_name');
            $table->string('address');
            $table->string('phone_number');
            $table->string('alt_phone_number');

            $table->enum('is_primary', ['yes', 'no'])->default('yes');

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
        Schema::dropIfExists('student_emergency_contact');
    }
};
