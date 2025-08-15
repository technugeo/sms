<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run()
    {
        Role::firstOrCreate(['name' => 'SA']);  // System Admin
        Role::firstOrCreate(['name' => 'AA']);  // Account Admin
        Role::firstOrCreate(['name' => 'NAO']); // Non Academic Officer
        Role::firstOrCreate(['name' => 'AO']);  // Academic Officer
        Role::firstOrCreate(['name' => 'S']);   // Student
    }
}
