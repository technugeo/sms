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

        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->string('mqa_institute_id');
            $table->string('name');
            $table->string('abbreviation');
            $table->string('category')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('district')->nullable();
            $table->string('dun')->nullable();
            $table->string('parliament')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('created_by')->default(1); 
            $table->unsignedBigInteger('updated_by')->default(1); 
            $table->unsignedBigInteger('deleted_by')->default(1); 
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institutions');
    }
};
