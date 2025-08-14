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

        Schema::create('password_reset', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('email');
            $table->string('token');
            $table->string('temp_hash_password');
            $table->string('password');
            $table->string('is_active');
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
        Schema::dropIfExists('password_reset');
    }
};
