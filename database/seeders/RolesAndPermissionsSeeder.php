<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define all roles
        $roles = ['SA', 'NAO', 'AA', 'AO', 'S'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // 2. Define permissions and role mappings
        $permissions = [
            'manage profile' => ['S'],
            'manage users' => ['SA'],
            'manage student' => ['SA', 'NAO', 'AO'],
            'manage staff' => ['SA', 'AA'],
            'manage institute' => ['SA'],
            'manage subject' => ['SA'],
            'manage regions' => ['SA'],
            'manage subregions' => ['SA'],
            'manage countries' => ['SA'],
            'manage states' => ['SA'],
            'manage parliaments' => ['SA'],
            'manage cities' => ['SA'],
            'view reports' => ['SA'],
            'edit settings' => ['SA'],
        ];

        // 3. Create permissions and assign to roles
        foreach ($permissions as $permName => $roleNames) {
            $permission = Permission::firstOrCreate(['name' => $permName]);
            $permission->syncRoles($roleNames);
        }

        // 4. Assign roles to existing users based on users.role column
        $roleMap = [
            'S' => 'S',
            'SA' => 'SA',
            'NAO' => 'NAO',
            'AA' => 'AA',
            'AO' => 'AO',
        ];

        foreach ($roleMap as $userRole => $spatieRole) {
            User::where('role', $userRole)->get()->each(function ($user) use ($spatieRole) {
                if (!$user->hasRole($spatieRole)) {
                    $user->assignRole($spatieRole);
                }
            });
        }
    }
}
