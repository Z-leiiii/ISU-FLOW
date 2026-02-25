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
                'default_credits_per_year' => 15,
                'is_paid' => true,
                'requires_documentation' => false,
                'is_accumulable' => true,
                'is_monetizable' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Sick Leave',
                'code' => 'SL',
                'description' => 'Paid sick leave for medical reasons',
                'default_credits_per_year' => 15,
                'is_paid' => true,
                'requires_documentation' => true,
                'is_accumulable' => true,
                'is_monetizable' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Mandatory Leave',
                'code' => 'ML',
                'description' => 'Mandatory leave as required by company/university policy',
                'default_credits_per_year' => 5,
                'is_paid' => true,
                'requires_documentation' => true,
                'is_accumulable' => false,
                'is_monetizable' => false,
                'is_active' => true,
            ],
        ];

        foreach ($leaveTypes as $leaveType) {
            LeaveType::create($leaveType);
        }
    }
}
