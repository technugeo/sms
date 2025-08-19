<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Permissions for students
        Permission::firstOrCreate(['name' => 'view_own_student_profile']);

        // Assign to student role
        $studentRole = Role::firstOrCreate(['name' => 'student']);
        $studentRole->givePermissionTo('view_own_student_profile');

        $this->command->info('student_profile assigned to student');

        
    }
}
