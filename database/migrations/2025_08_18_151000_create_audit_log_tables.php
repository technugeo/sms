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
        Schema::create('audit_log', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date_time')->useCurrent();
            $table->string('action_by', 255);
            $table->enum('action_type', ['create','update','delete','print','download','login','logout']);
            $table->string('module', 50);
            $table->unsignedBigInteger('record_id')->nullable();
            $table->text('old_data')->nullable();
            $table->text('new_data')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();    
            $table->softDeletes();   

            // Indexes for faster querying
            $table->index('module');
            $table->index('record_id');
            $table->index('action_type');
            $table->index('date_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_log');
    }
};
