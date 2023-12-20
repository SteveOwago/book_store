<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         /**
     * Run the database seeds.
     *
     * @return void
     */

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'admin_access']);
        Permission::create(['name' => 'user_access']);

        //Create Roles
        $roleAdmin = Role::create(['name' => 'Admin']);
        $roleUser = Role::create(['name' => 'User']);

        // Assign Permissions To Role
        $roleAdmin->givePermissionTo(Permission::all());
        $roleUser->givePermissionTo(['user_access']);

    }
}
