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
        Schema::create('subregions', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->string('name');
            $table->text('translations');
            $table->unsignedMediumInteger('region_id');
            $table->boolean('flag')->default(true);
            $table->string('wikiDataId');
            $table->timestamps();

            $table->foreign('region_id')->references('id')->on('regions')->restrictOnDelete();
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('region_id');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_regions');
    }
};
