<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserRoleSeeder extends Seeder
{
    public function run()
    {
        // Assign role to specific users
        $admin = User::where('email', 'superadmin@unipulse.com')->first();
        if ($admin) {
            $admin->assignRole('SA'); // System Admin
        }

        $accountAdmin = User::where('email', 'account@example.com')->first();
        if ($accountAdmin) {
            $accountAdmin->assignRole('AA'); // Account Admin
        }

        $student = User::where('email', 'student@example.com')->first();
        if ($student) {
            $student->assignRole('S'); // Student
        }

        // Add more users here if needed
    }
}
