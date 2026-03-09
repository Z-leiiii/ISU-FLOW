<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveCredit;
use Carbon\Carbon;

class LeaveCreditSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['employee', 'department_head']);
        })->get();

        $leaveTypes = LeaveType::where('is_paid', true)->get();

        foreach ($users as $user) {
            foreach ($leaveTypes as $leaveType) {
                // Calculate earned leave based on user's designation and hire date
                $earned = $this->calculateEarnedLeave($user, $leaveType);
                
                if ($earned > 0) {
                    LeaveCredit::create([
                        'user_id' => $user->id,
                        'leave_type_id' => $leaveType->id,
                        'total_earned' => $earned,
                        'total_used' => 0,
                        'as_of_date' => now(),
                        'remarks' => 'Initial leave credit allocation',
                    ]);
                }
            }
        }
    }

    private function calculateEarnedLeave($user, $leaveType): float
    {
        // If user has no designation, no leave credits
        if (!$user->designation) {
            return 0;
        }

        // Check if this leave type is earned by this designation
        if ($leaveType->code === 'VL' && !$user->designation->earns_vacation_leave) {
            return 0;
        }
        if ($leaveType->code === 'SL' && !$user->designation->earns_sick_leave) {
            return 0;
        }

        // Calculate months since hire date
        $hireDate = Carbon::parse($user->date_hired);
        $currentDate = now();
        $monthsWorked = $hireDate->diffInMonths($currentDate);

        // Calculate earned leave based on rate
        $rate = 0;
        if ($leaveType->code === 'VL') {
            $rate = $user->designation->vacation_leave_rate;
        } elseif ($leaveType->code === 'SL') {
            $rate = $user->designation->sick_leave_rate;
        }

        return $monthsWorked * $rate;
    }
}
