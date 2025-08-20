<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Only truncate users in local environment
        if (app()->environment('local')) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('users')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // Clear cached roles/permissions before seeding
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Generate permissions from Shield (Filament)
        Artisan::call('shield:generate --all --panel=admin --ignore-existing-policies');

        // Create custom permissions (if not exist already)
        $extraPermissions = [
            'view_on_student_profile',
            'view_on_staff_profile',
        ];

        foreach ($extraPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Fetch fresh list of all permissions
        $allPermissions = Permission::pluck('name')->toArray();

        // Define student permissions exactly as in your SQL
        $studentPermissions = [
            'update_student',
            'reorder_student',
            'view_student::emergency::contact',
            'view_any_student::emergency::contact',
            'create_student::emergency::contact',
            'update_student::emergency::contact',
            'reorder_student::emergency::contact',
            'delete_student::emergency::contact',
            'delete_any_student::emergency::contact',
            'view_student::guardian',
            'view_any_student::guardian',
            'create_student::guardian',
            'update_student::guardian',
            'reorder_student::guardian',
            'delete_student::guardian',
            'delete_any_student::guardian',
            'view_on_student_profile',
        ];

        $aoPermissions = [
            'view_student',
            'view_any_student',
            'create_student',
            'update_student',
            'reorder_student',
            'delete_student',
            'delete_any_student',
            'view_student::emergency::contact',
            'view_any_student::emergency::contact',
            'create_student::emergency::contact',
            'update_student::emergency::contact',
            'reorder_student::emergency::contact',
            'delete_student::emergency::contact',
            'delete_any_student::emergency::contact',
            'view_student::guardian',
            'view_any_student::guardian',
            'create_student::guardian',
            'update_student::guardian',
            'reorder_student::guardian',
            'delete_student::guardian',
            'delete_any_student::guardian',
            'view_on_staff_profile',
        ];
        $nonAcademicOfficerPermissions = [
            'view_staff',
            'view_any_staff',
            'create_staff',
            'update_staff',
            'reorder_staff',
            'delete_staff',
            'delete_any_staff',
            'view_student',
            'view_any_student',
            'create_student',
            'update_student',
            'reorder_student',
            'delete_student',
            'delete_any_student',
            'view_student::emergency::contact',
            'view_any_student::emergency::contact',
            'create_student::emergency::contact',
            'update_student::emergency::contact',
            'reorder_student::emergency::contact',
            'delete_student::emergency::contact',
            'delete_any_student::emergency::contact',
            'view_student::guardian',
            'view_any_student::guardian',
            'create_student::guardian',
            'update_student::guardian',
            'reorder_student::guardian',
            'delete_student::guardian',
            'delete_any_student::guardian',
            'view_on_staff_profile',
        ];

        // Define roles and their permissions
        $roles = [
            'student' => $studentPermissions, // Exact permissions from SQL
            'academic_officer' => $aoPermissions,
            'non_academic_officer' => $nonAcademicOfficerPermissions,
            'account_admin'  => $nonAcademicOfficerPermissions,
            'system_admin' => $allPermissions,
            'super_admin' => $allPermissions,
        ];

        // Create roles and sync permissions
        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }


        // Create or update superadmin user
        $user = User::firstOrCreate(
            ['email' => 'superadmin@unipulse.com'],
            [
                'name' => 'superadmin',
                'password' => Hash::make('password'), // Replace with secure password
                'email_verified_at' => now(),
                'profile_id' => 1,
                'profile_type' => 'App\Models\Employee',
                'role' => 'super_admin',
                'user_type' => 'Employee',
            ]
        );

        // Ensure super_admin role exists
        $saRole = Role::firstOrCreate(['name' => 'super_admin']);

        // Assign role to user
        $user->syncRoles([$saRole]);

        // Give super_admin all permissions (in case new ones were added later)
        $saRole->syncPermissions(Permission::all());

        // Register as Filament Shield super-admin
        Artisan::call('shield:super-admin', [
            '--user' => $user->id,
            '--panel' => 'admin',
        ]);

        // Clear cached permissions after seeding
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('Superadmin created with full permissions.');
    }
}
