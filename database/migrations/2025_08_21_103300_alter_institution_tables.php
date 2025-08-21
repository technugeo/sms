<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            // Change existing columns from unsignedBigInteger to string
            $table->string('created_by')->nullable()->default('superadmin@unipulse.com')->change();
            $table->string('updated_by')->nullable()->change();
            $table->string('deleted_by')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            // Revert columns back to unsignedBigInteger with default 1
            $table->unsignedBigInteger('created_by')->default(1)->change();
            $table->unsignedBigInteger('updated_by')->default(1)->change();
            $table->unsignedBigInteger('deleted_by')->default(1)->change();
        });
    }
};
