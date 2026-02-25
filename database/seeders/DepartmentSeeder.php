<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Information Technology',
                'code' => 'IT',
                'description' => 'Handles all IT infrastructure, systems, and technical support',
                'head_of_department' => null, // Will be set when admin user is created
                'is_active' => true,
            ],
            [
                'name' => 'Human Resources',
                'code' => 'HR',
                'description' => 'Manages employee relations, benefits, and leave administration',
                'head_of_department' => null, // Will be set when HR user is created
                'is_active' => true,
            ],
            [
                'name' => 'Academic Affairs',
                'code' => 'AA',
                'description' => 'Oversees academic programs, faculty matters, and curriculum development',
                'head_of_department' => null, // Will be set when department Head is created
                'is_active' => true,
            ],
            [
                'name' => 'Finance',
                'code' => 'FIN',
                'description' => 'Manages financial operations, budgeting, and accounting',
                'head_of_department' => null, // Will be set when Finance Head is created
                'is_active' => true,
            ],
            [
                'name' => 'Administrative Office',
                'code' => 'ADMIN',
                'description' => 'Provides administrative support and services to all departments',
                'head_of_department' => null, // Will be set when Admin user is created
                'is_active' => true,
            ],
            [
                'name' => 'Library Services',
                'code' => 'LIB',
                'description' => 'Manages library resources, information services, and academic support',
                'head_of_department' => null, // Will be set when Library Head is created
                'is_active' => true,
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
