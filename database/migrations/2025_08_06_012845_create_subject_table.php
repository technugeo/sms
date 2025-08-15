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

        Schema::create('lib_sem_subjects', function (Blueprint $table) {
            $table->id();
            $table->string('subject_code');
            $table->string('subject_name');
            $table->integer('semester')->default(0);
            $table->integer('credit_hour');
            $table->string('is_core')->nullable();
            $table->string('subject_status')->nullable();
            $table->enum('status', ['yes', 'no'])->default('yes');
            

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lib_sem_subjects');
    }
};
