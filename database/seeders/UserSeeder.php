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
            'department_id' => 1,
            'designation_id' => 6, // HR Manager designation
            'date_hired' => now(),
            'salary' => 50000.00,
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
            'department_id' => 2,
            'designation_id' => 6, // HR Manager designation
            'date_hired' => now(),
            'salary' => 45000.00,
            'contact_number' => '123-456-7891',
            'address' => 'ISU Main Campus',
            'is_active' => true,
        ]);

        $hr->assignRole('hr');

        // Create sample employee
        $employee = User::create([
            'employee_id' => 'EMP001',
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'middle_name' => 'Santos',
            'email' => 'juan.delacruz@isu.edu.ph',
            'password' => Hash::make('password'),
            'department_id' => 3, // Academic Affairs
            'designation_id' => 1, // Professor
            'date_hired' => now()->subYears(3),
            'salary' => 35000.00,
            'contact_number' => '123-456-7892',
            'address' => 'ISU Main Campus',
            'is_active' => true,
        ]);

        $employee->assignRole('employee');
    }
}
