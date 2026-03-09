<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Designation;

class DesignationSeeder extends Seeder
{
    public function run(): void
    {
        $designations = [
            [
                'title' => 'Professor',
                'code' => 'PROF',
                'description' => 'Senior faculty position with full professor rank',
                'earns_vacation_leave' => true,
                'earns_sick_leave' => true,
                'vacation_leave_rate' => 1.25,
                'sick_leave_rate' => 1.25,
                'is_active' => true,
            ],
            [
                'title' => 'Associate Professor',
                'code' => 'ASSOC_PROF',
                'description' => 'Mid-level faculty position with associate professor rank',
                'earns_vacation_leave' => true,
                'earns_sick_leave' => true,
                'vacation_leave_rate' => 1.25,
                'sick_leave_rate' => 1.25,
                'is_active' => true,
            ],
            [
                'title' => 'Assistant Professor',
                'code' => 'ASST_PROF',
                'description' => 'Entry-level faculty position with assistant professor rank',
                'earns_vacation_leave' => true,
                'earns_sick_leave' => true,
                'vacation_leave_rate' => 1.25,
                'sick_leave_rate' => 1.25,
                'is_active' => true,
            ],
            [
                'title' => 'Instructor',
                'code' => 'INST',
                'description' => 'Teaching faculty position',
                'earns_vacation_leave' => true,
                'earns_sick_leave' => true,
                'vacation_leave_rate' => 1.25,
                'sick_leave_rate' => 1.25,
                'is_active' => true,
            ],
            [
                'title' => 'Administrative Assistant',
                'code' => 'ADMIN_ASSIST',
                'description' => 'Administrative support staff',
                'earns_vacation_leave' => true,
                'earns_sick_leave' => true,
                'vacation_leave_rate' => 1.25,
                'sick_leave_rate' => 1.25,
                'is_active' => true,
            ],
            [
                'title' => 'HR Manager',
                'code' => 'HR_MGR',
                'description' => 'Human Resources department manager',
                'earns_vacation_leave' => true,
                'earns_sick_leave' => true,
                'vacation_leave_rate' => 1.25,
                'sick_leave_rate' => 1.25,
                'is_active' => true,
            ],
            [
                'title' => 'IT Manager',
                'code' => 'IT_MGR',
                'description' => 'Information Technology department manager',
                'earns_vacation_leave' => true,
                'earns_sick_leave' => true,
                'vacation_leave_rate' => 1.25,
                'sick_leave_rate' => 1.25,
                'is_active' => true,
            ],
            [
                'title' => 'Department Head',
                'code' => 'DEPT_HEAD',
                'description' => 'Head of academic department',
                'earns_vacation_leave' => true,
                'earns_sick_leave' => true,
                'vacation_leave_rate' => 1.25,
                'sick_leave_rate' => 1.25,
                'is_active' => true,
            ],
        ];

        foreach ($designations as $designation) {
            Designation::create($designation);
        }
    }
}
