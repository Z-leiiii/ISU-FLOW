<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Department;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only create admin user for system access
        $admin = User::create([
            'employee_id' => 'ADMIN001',
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'middle_name' => null,
            'email' => 'admin@isu.edu.ph',
            'password' => Hash::make('password'),
            'department_id' => 1, // Will be updated to actual admin department
            'position' => 'System Administrator',
            'employment_type' => 'regular',
            'hire_date' => now(),
            'contact_number' => '123-456-7890',
            'address' => 'ISU Main Campus',
            'is_active' => true,
        ]);

        $admin->assignRole('admin');

        // Create HR user
        $hr = User::create([
            'employee_id' => 'HR001',
            'first_name' => 'Human',
            'last_name' => 'Resources',
            'middle_name' => null,
            'email' => 'hr@isu.edu.ph',
            'password' => Hash::make('password'),
            'department_id' => 2, // Will be updated to actual HR department
            'position' => 'HR Manager',
            'employment_type' => 'regular',
            'hire_date' => now(),
            'contact_number' => '123-456-7891',
            'address' => 'ISU Main Campus',
            'is_active' => true,
        ]);

        $hr->assignRole('hr');

        // Update department heads after creating users
        $itDepartment = Department::find(1);
        if ($itDepartment) {
            $itDepartment->update(['head_of_department' => $admin->id]);
        }

        $hrDepartment = Department::find(2);
        if ($hrDepartment) {
            $hrDepartment->update(['head_of_department' => $hr->id]);
        }
        Department::where('id', 4)->update(['head_of_department' => 6]); // Academic Affairs Head
    }
}
