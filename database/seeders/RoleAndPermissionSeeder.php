<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'view-dashboard',
            'view-leave-applications',
            'create-leave-applications',
            'edit-leave-applications',
            'delete-leave-applications',
            'approve-leave-applications',
            'reject-leave-applications',
            'view-leave-credits',
            'manage-leave-credits',
            'view-attendance',
            'manage-attendance',
            'view-reports',
            'generate-reports',
            'manage-users',
            'manage-departments',
            'view-system-settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $roles = [
            'admin' => [
                'view-dashboard',
                'view-leave-applications',
                'create-leave-applications',
                'edit-leave-applications',
                'delete-leave-applications',
                'approve-leave-applications',
                'reject-leave-applications',
                'view-leave-credits',
                'manage-leave-credits',
                'view-attendance',
                'manage-attendance',
                'view-reports',
                'generate-reports',
                'manage-users',
                'manage-departments',
                'view-system-settings',
            ],
            'hr' => [
                'view-dashboard',
                'view-leave-applications',
                'approve-leave-applications',
                'reject-leave-applications',
                'view-leave-credits',
                'manage-leave-credits',
                'view-attendance',
                'view-reports',
                'generate-reports',
            ],
            'department_head' => [
                'view-dashboard',
                'view-leave-applications',
                'approve-leave-applications',
                'reject-leave-applications',
                'view-leave-credits',
                'view-attendance',
                'view-reports',
            ],
            'employee' => [
                'view-dashboard',
                'view-leave-applications',
                'create-leave-applications',
                'edit-leave-applications',
                'delete-leave-applications',
                'view-leave-credits',
                'view-attendance',
                'view-reports',
            ],
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($permissions);
        }
    }
}
