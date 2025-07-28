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
        Schema::create('countries', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->string('name',100);
            $table->string('iso3',3)->nullable();
            $table->string('numeric_code',3)->nullable();
            $table->string('iso2',2)->nullable();
            $table->string('phonecode')->nullable();
            $table->string('capital')->nullable();
            $table->string('currency')->nullable();
            $table->string('currency_name')->nullable();
            $table->string('currency_symbol')->nullable();
            $table->string('tld')->nullable();
            $table->string('native')->nullable();
            $table->string('region')->nullable();
            $table->unsignedMediumInteger('region_id');
            $table->string('subregion')->nullable();
            $table->unsignedMediumInteger('subregion_id')->nullable()->constrained('subregions')->restrictOnDelete();
            $table->string('nationality')->nullable();
            $table->text('timezones')->nullable();
            $table->text('translations')->nullable();
            $table->decimal('latitude',10,8)->nullable();
            $table->decimal('longitude',11,8)->nullable();
            $table->string('emoji')->nullable();
            $table->string('emojiU')->nullable();
            $table->timestamps();
            $table->boolean('flag')->default(true);
            $table->string('wikiDataId');

            $table->foreign('region_id')->references('id')->on('regions')->restrictOnDelete();
            $table->foreign('subregion_id')->references('id')->on('subregions')->restrictOnDelete();
            $table->index('name');
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('iso3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
