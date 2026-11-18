<?php

namespace Database\Seeders;

use App\Models\RolePermission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (array_keys(config('role_permissions.roles')) as $role) {
            foreach (RolePermission::availableKeys($role) as $permission) {
                RolePermission::firstOrCreate(['role' => $role, 'permission' => $permission], ['is_allowed' => true]);
            }
        }
    }
}
