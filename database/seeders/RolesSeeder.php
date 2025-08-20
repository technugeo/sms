<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run()
    {
        Role::firstOrCreate(['name' => 'system_admin']);  // System Admin
        Role::firstOrCreate(['name' => 'account_admin']);  // Account Admin
        Role::firstOrCreate(['name' => 'non_academic_officer']); // Non Academic Officer
        Role::firstOrCreate(['name' => 'academic_officer']);  // Academic Officer
        Role::firstOrCreate(['name' => 'student']);   // Student
    }
}
