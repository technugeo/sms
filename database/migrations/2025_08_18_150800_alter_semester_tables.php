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
        Schema::table('lib_semester', function (Blueprint $table) {

            $table->string('created_by', 200)->nullable()->change();
            $table->string('updated_by', 200)->nullable()->change();
            $table->string('deleted_by', 200)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lib_semester', function (Blueprint $table) {
            $table->integer('created_by')->change();
            $table->integer('updated_by')->change();
            $table->integer('deleted_by')->change();
        });
    }
};
