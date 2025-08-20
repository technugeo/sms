<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_log', function (Blueprint $table) {
            // Alter the existing enum column to add new values
            $table->enum('action_type', ['create','update','delete','print','download','login','logout','restore','force_delete'])
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('audit_log', function (Blueprint $table) {
            // Revert back to the original enum values
            $table->enum('action_type', ['create','update','delete','print','download','login','logout'])
                  ->change();
        });
    }
};
