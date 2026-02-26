<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LeaveType;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leaveTypes = [
            [
                'name' => 'Vacation Leave',
                'code' => 'VL',
                'description' => 'Paid vacation leave for rest and recreation',
                'requires_documentation' => false,
                'is_paid' => true,
                'is_active' => true,
                'max_days_per_year' => 15,
            ],
            [
                'name' => 'Sick Leave',
                'code' => 'SL',
                'description' => 'Paid sick leave for medical reasons',
                'requires_documentation' => true,
                'is_paid' => true,
                'is_active' => true,
                'max_days_per_year' => 15,
            ],
            [
                'name' => 'Maternity Leave',
                'code' => 'ML',
                'description' => 'Paid maternity leave for female employees',
                'requires_documentation' => true,
                'is_paid' => true,
                'is_active' => true,
                'max_days_per_year' => 105,
            ],
            [
                'name' => 'Paternity Leave',
                'code' => 'PL',
                'description' => 'Paid paternity leave for male employees',
                'requires_documentation' => true,
                'is_paid' => true,
                'is_active' => true,
                'max_days_per_year' => 7,
            ],
            [
                'name' => 'Special Leave Benefits for Women',
                'code' => 'SLBW',
                'description' => 'Special leave benefits for women under RA 9710',
                'requires_documentation' => true,
                'is_paid' => true,
                'is_active' => true,
                'max_days_per_year' => 2,
            ],
            [
                'name' => 'Solo Parent Leave',
                'code' => 'SPL',
                'description' => 'Leave benefits for solo parents under RA 8972',
                'requires_documentation' => true,
                'is_paid' => true,
                'is_active' => true,
                'max_days_per_year' => 7,
            ],
            [
                'name' => 'Emergency Leave',
                'code' => 'EL',
                'description' => 'Emergency leave for urgent personal matters',
                'requires_documentation' => false,
                'is_paid' => true,
                'is_active' => true,
                'max_days_per_year' => 3,
            ],
            [
                'name' => 'Special Privilege Leave',
                'code' => 'SPLV',
                'description' => 'Special privilege leave for personal reasons',
                'requires_documentation' => false,
                'is_paid' => true,
                'is_active' => true,
                'max_days_per_year' => 3,
            ],
            [
                'name' => 'Study Leave',
                'code' => 'STL',
                'description' => 'Leave for professional development and study',
                'requires_documentation' => true,
                'is_paid' => true,
                'is_active' => true,
                'max_days_per_year' => 6,
            ],
            [
                'name' => 'Leave Without Pay',
                'code' => 'LWOP',
                'description' => 'Leave without pay for extended periods',
                'requires_documentation' => false,
                'is_paid' => false,
                'is_active' => true,
                'max_days_per_year' => null,
            ],
        ];

        foreach ($leaveTypes as $leaveType) {
            LeaveType::create($leaveType);
        }
    }
}
