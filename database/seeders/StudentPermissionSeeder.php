<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class StudentPermissionSeeder extends Seeder
{
    public function run()
    {
        // Ensure permission exists
        $permission = Permission::firstOrCreate(['name' => 'view_own_student_profile']);

        // Ensure student role exists
        $studentRole = Role::firstOrCreate(['name' => 'student']);

        // Assign permission to the role
        if (! $studentRole->hasPermissionTo($permission)) {
            $studentRole->givePermissionTo($permission);
        }

        $this->command->info('student_profile permission created.');
        
    }
}
