<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\LeaveBalance;
use App\Models\LeaveType;

class LeaveBalanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $leaveTypes = LeaveType::all();
        $currentYear = now()->year;

        foreach ($users as $user) {
            foreach ($leaveTypes as $leaveType) {
                // Check if leave balance already exists
                $existingBalance = LeaveBalance::where('user_id', $user->id)
                    ->where('leave_type_id', $leaveType->id)
                    ->where('year', $currentYear)
                    ->first();

                if (!$existingBalance) {
                    // Create initial leave balance based on leave type
                    $credits = $this->getInitialCredits($leaveType->name);
                    
                    LeaveBalance::create([
                        'user_id' => $user->id,
                        'leave_type_id' => $leaveType->id,
                        'total_earned' => $credits,
                        'total_used' => 0,
                        'current_balance' => $credits,
                        'carry_over' => 0,
                        'year' => $currentYear,
                    ]);
                }
            }
        }
    }

    /**
     * Get initial credits based on leave type.
     */
    private function getInitialCredits($leaveTypeName)
    {
        switch (strtolower($leaveTypeName)) {
            case 'vacation leave':
                return 15;
            case 'sick leave':
                return 10;
            case 'mandatory leave':
                return 5;
            default:
                return 0;
        }
    }
}
