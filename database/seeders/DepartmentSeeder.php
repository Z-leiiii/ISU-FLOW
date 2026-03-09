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
                'is_active' => true,
            ],
            [
                'name' => 'Human Resources',
                'code' => 'HR',
                'description' => 'Manages employee relations, benefits, and leave administration',
                'is_active' => true,
            ],
            [
                'name' => 'Academic Affairs',
                'code' => 'AA',
                'description' => 'Oversees academic programs, faculty matters, and curriculum development',
                'is_active' => true,
            ],
            [
                'name' => 'Finance',
                'code' => 'FIN',
                'description' => 'Manages financial operations, budgeting, and accounting',
                'is_active' => true,
            ],
            [
                'name' => 'Administrative Office',
                'code' => 'ADMIN',
                'description' => 'Provides administrative support and services to all departments',
                'is_active' => true,
            ],
            [
                'name' => 'Library Services',
                'code' => 'LIB',
                'description' => 'Manages library resources, information services, and academic support',
                'is_active' => true,
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
