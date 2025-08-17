<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate users table
        DB::table('users')->truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create the superadmin user
        $user = User::firstOrCreate(
            ['email' => 'superadmin@unipulse.com'],
            [
                'name' => 'superadmin',
                'password' => Hash::make('password'), // Change to a secure password
                'email_verified_at' => now(),
                'profile_id' => 1,
                'profile_type' => 'App\Models\Employee',
                'user_type' => 'Employee',
            ]
        );

        // Assign the SA role
        $saRole = Role::where('name', 'SA')->first();
        if ($saRole) {
            $user->assignRole($saRole);
        }
    }
}
