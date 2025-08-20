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

        Artisan::call('shield:generate --all --panel=admin --ignore-existing-policies');

        $permissions = [
            'view_on_student_profile', 
            'view_on_staff_profile',  
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $roles = [
            'student' => [
                'view_on_student_profile',
            ],
            'academic_officer' => [
                'view_on_staff_profile',
            ],
            'non_academic_officer' => [
                'view_on_staff_profile',
            ],
            'account_admin' => [
                'view_on_staff_profile',
            ],
            'system_admin' => Permission::all()->pluck('name')->toArray(),
            'super_admin' => Permission::all()->pluck('name')->toArray(),
        ];

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

        // Give super_admin role all permissions
        $saRole->givePermissionTo(Permission::all());

        // Register as Filament Shield super-admin
        Artisan::call('shield:super-admin', [
            '--user' => $user->id,
            '--panel' => 'admin',
        ]);

        // Clear cached permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('Superadmin created with full permissions.');
    }
}
