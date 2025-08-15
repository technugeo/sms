<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            // Drop the old primary key on email and drop the column
            $table->dropPrimary('PRIMARY');
            $table->dropColumn('email');

            // Add new id as primary key
            $table->bigIncrements('id')->first();

            // Add new email column
            $table->string('email')->after('id');

            // Add other missing columns
            $table->unsignedBigInteger('user_id')->nullable(false)->after('email');
            $table->string('temp_hash_password')->nullable(false)->after('token');
            $table->string('password')->nullable(false)->after('temp_hash_password');
            $table->enum('is_active', ['yes', 'no'])->default('yes')->after('password');
            $table->timestamp('updated_at')->nullable()->after('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->dropColumn(['id', 'email', 'user_id', 'temp_hash_password', 'password', 'is_active', 'updated_at']);
            $table->string('email')->primary();
        });
    }
};
