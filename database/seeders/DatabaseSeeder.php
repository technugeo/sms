<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(WorldSeeder::class); // countries, regions, etc.

        //$this->call(RolesSeeder::class); // create roles

        $this->call(SuperAdminSeeder::class); // create superadmin user and assign SA role

        // $this->call(StudentPermissionSeeder::class);
        // $this->call(RolesAndPermissionsSeeder::class); // create permissions and assign to roles

        $this->call(InstituteSeeder::class); // institutes
    }

}
