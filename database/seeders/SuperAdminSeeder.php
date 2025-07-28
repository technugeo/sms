<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        User::firstOrCreate(
            ['email' => 'superadmin@unipulse.com'],
            [
                'name' => 'superadmin',
                'password' => Hash::make('password'), // Change to secure password
                'email_verified_at' => now(),
                'profile_id' => 1,
                'profile_type' => 'App\Models\Employee',
            ]
        );
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
