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
        Schema::create('cities', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->string('name');
            $table->unsignedMediumInteger('state_id');
            $table->string('state_code');
            $table->unsignedMediumInteger('country_id');
            $table->char('country_code', 2);
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->timestamps();
            $table->boolean('flag')->default(1); // tinyint(1) NOT NULL DEFAULT '1'
            $table->string('wikiDataId')->nullable()->comment('Rapid API GeoDB Cities'); // varchar(255) DEFAULT NULL

            $table->foreign('state_id')->references('id')->on('states')->restrictOnDelete();
            $table->foreign('country_id')->references('id')->on('countries')->restrictOnDelete();
            // Indexes
            $table->index('state_id');
            $table->index('country_id');
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
