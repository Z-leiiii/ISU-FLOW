<?php

namespace App\Services;

use App\Models\User;
use App\Models\DTR;
use App\Models\LeaveBalance;
use App\Models\LeaveApplication;
use App\Models\Notification;
use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonthlySyncService
{
    /**
     * Perform monthly synchronization for all faculty.
     */
    public function syncMonthly(int $year, int $month): array
    {
        $results = [
            'synced_employees' => 0,
            'dtr_processed' => 0,
            'leave_balances_updated' => 0,
            'cto_earned' => 0,
            'notifications_sent' => 0,
            'errors' => []
        ];

        try {
            // Get all faculty users
            $faculty = User::whereHas('roles', function($query) {
                $query->where('name', 'faculty');
            })->get();

            foreach ($faculty as $user) {
                try {
                    $this->syncEmployee($user, $year, $month, $results);
                    $results['synced_employees']++;
                } catch (\Exception $e) {
                    $results['errors'][] = "Error syncing employee {$user->id}: " . $e->getMessage();
                    Log::error("Monthly sync error for user {$user->id}", [
                        'error' => $e->getMessage(),
                        'year' => $year,
                        'month' => $month
                    ]);
                }
            }

            // Create summary notification for HR
            $this->createHRSummaryNotification($year, $month, $results);

        } catch (\Exception $e) {
            $results['errors'][] = "General sync error: " . $e->getMessage();
            Log::error("Monthly sync general error", [
                'error' => $e->getMessage(),
                'year' => $year,
                'month' => $month
            ]);
        }

        return $results;
    }

    /**
     * Sync individual employee data.
     */
    private function syncEmployee(User $user, int $year, int $month, array &$results): void
    {
        // Process DTR data
        $dtrResults = $this->processEmployeeDTR($user, $year, $month);
        $results['dtr_processed'] += $dtrResults['processed'];
        $results['cto_earned'] += $dtrResults['cto_hours'];

        // Update leave balances
        $balanceResults = $this->updateEmployeeLeaveBalances($user, $year);
        $results['leave_balances_updated'] += $balanceResults['updated'];
        $results['notifications_sent'] += $balanceResults['notifications'];

        // Check for leave without pay
        $this->checkLeaveWithoutPay($user, $year, $month, $results);
    }

    /**
     * Process employee DTR for the month.
     */
    private function processEmployeeDTR(User $user, int $year, int $month): array
    {
        $results = [
            'processed' => 0,
            'cto_hours' => 0
        ];

        // Get all approved DTR for the month
        $dtrs = DTR::forUser($user->id)
            ->forMonth($year, $month)
            ->approved()
            ->get();

        $totalOvertimeHours = $dtrs->sum('overtime_hours');
        $totalRegularHours = $dtrs->sum('hours_worked');

        if ($totalOvertimeHours > 0) {
            // Get or create CTO leave balance
            $ctoLeaveType = LeaveType::where('name', 'CTO')->first();
            if ($ctoLeaveType) {
                $ctoBalance = LeaveBalance::firstOrCreate([
                    'user_id' => $user->id,
                    'leave_type_id' => $ctoLeaveType->id,
                    'year' => $year
                ], [
                    'total_earned' => 0,
                    'total_used' => 0,
                    'current_balance' => 0,
                    'carry_over' => 0,
                    'last_updated' => now()
                ]);

                // Add overtime hours to CTO balance
                $ctoBalance->total_earned += $totalOvertimeHours;
                $ctoBalance->current_balance += $totalOvertimeHours;
                $ctoBalance->last_updated = now();
                $ctoBalance->save();

                // Create notification for CTO earned
                Notification::createCTOEarnedNotification($user->id, $totalOvertimeHours);

                $results['cto_hours'] = $totalOvertimeHours;
            }
        }

        $results['processed'] = $dtrs->count();
        return $results;
    }

    /**
     * Update employee leave balances.
     */
    private function updateEmployeeLeaveBalances(User $user, int $year): array
    {
        $results = [
            'updated' => 0,
            'notifications' => 0
        ];

        $leaveTypes = LeaveType::all();

        foreach ($leaveTypes as $leaveType) {
            try {
                $computation = LeaveCreditService::computeLeaveCredits($user, $leaveType);
                
                $balance = LeaveBalance::updateOrCreate([
                    'user_id' => $user->id,
                    'leave_type_id' => $leaveType->id,
                    'year' => $year
                ], [
                    'total_earned' => $computation['total_earned'],
                    'total_used' => $computation['total_used'],
                    'current_balance' => $computation['balance'],
                    'computed_at' => now(),
                    'last_updated' => now()
                ]);

                $results['updated']++;

                // Check for low balance alerts
                if ($computation['balance'] < 1) {
                    Notification::createCriticalBalanceNotification(
                        $user->id, 
                        $leaveType->name, 
                        $computation['balance']
                    );
                    $results['notifications']++;
                } elseif ($computation['balance'] < 3) {
                    Notification::createLowBalanceNotification(
                        $user->id, 
                        $leaveType->name, 
                        $computation['balance']
                    );
                    $results['notifications']++;
                }

            } catch (\Exception $e) {
                Log::error("Error updating leave balance for user {$user->id}, leave type {$leaveType->id}", [
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $results;
    }

    /**
     * Check for leave without pay and create notifications.
     */
    private function checkLeaveWithoutPay(User $user, int $year, int $month, array &$results): void
    {
        $lwpLeaveType = LeaveType::where('name', 'Leave Without Pay')->first();
        if (!$lwpLeaveType) {
            return;
        }

        $lwpDays = LeaveApplication::where('user_id', $user->id)
            ->where('leave_type_id', $lwpLeaveType->id)
            ->where('status', 'approved')
            ->whereMonth('start_date', $month)
            ->whereYear('start_date', $year)
            ->sum('days_requested');

        if ($lwpDays > 0) {
            Notification::createLeaveWithoutPayNotification($user->id, $lwpDays);
            $results['notifications_sent']++;
        }
    }

    /**
     * Create summary notification for HR.
     */
    private function createHRSummaryNotification(int $year, int $month, array $results): void
    {
        $hrUsers = User::whereHas('roles', function($query) {
            $query->where('name', 'hr');
        })->get();

        $monthName = Carbon::create($year, $month, 1)->format('F Y');
        $message = "Monthly synchronization completed for {$monthName}. " .
                   "Synced: {$results['synced_employees']} employees, " .
                   "DTRs: {$results['dtr_processed']}, " .
                   "CTO earned: {$results['cto_earned']} hours, " .
                   "Notifications: {$results['notifications_sent']}";

        foreach ($hrUsers as $hrUser) {
            Notification::create([
                'user_id' => $hrUser->id,
                'title' => 'Monthly Sync Complete',
                'message' => $message,
                'type' => 'monthly_sync_complete',
                'priority' => 'info',
                'data' => [
                    'year' => $year,
                    'month' => $month,
                    'results' => $results
                ],
                'expires_at' => now()->addDays(7)
            ]);
        }
    }

    /**
     * Check for pending DTR submissions and send reminders.
     */
    public function checkPendingDTRSubmissions(): array
    {
        $results = [
            'employees_checked' => 0,
            'reminders_sent' => 0,
            'pending_days' => 0
        ];

        $currentMonth = Carbon::now();
        $faculty = User::whereHas('roles', function($query) {
            $query->where('name', 'faculty');
        })->get();

        foreach ($faculty as $user) {
            $pendingDays = $this->getPendingDTRDays($user->id, $currentMonth->year, $currentMonth->month);
            $results['employees_checked']++;
            $results['pending_days'] += count($pendingDays);

            if (count($pendingDays) > 0) {
                Notification::createDTRPendingNotification($user->id, count($pendingDays));
                $results['reminders_sent']++;
            }
        }

        return $results;
    }

    /**
     * Get pending DTR days for a user.
     */
    private function getPendingDTRDays(int $userId, int $year, int $month): array
    {
        $pendingDays = [];
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($year, $month, $day);
            
            // Skip weekends and future dates
            if ($date->isWeekend() || $date->isFuture()) {
                continue;
            }
            
            $dtr = DTR::where('user_id', $userId)
                ->where('date', $date->format('Y-m-d'))
                ->first();
            
            if (!$dtr || $dtr->status === 'pending') {
                $pendingDays[] = $date->format('Y-m-d');
            }
        }
        
        return $pendingDays;
    }

    /**
     * Generate monthly attendance report.
     */
    public function generateAttendanceReport(int $year, int $month): array
    {
        $report = [
            'period' => Carbon::create($year, $month, 1)->format('F Y'),
            'total_employees' => 0,
            'total_days' => 0,
            'total_hours' => 0,
            'total_overtime' => 0,
            'attendance_rate' => 0,
            'departments' => []
        ];

        $faculty = User::whereHas('roles', function($query) {
            $query->where('name', 'faculty');
        })->get();

        $report['total_employees'] = $faculty->count();

        foreach ($faculty as $user) {
            $dtrs = DTR::forUser($user->id)
                ->forMonth($year, $month)
                ->approved()
                ->get();

            $userStats = [
                'name' => $user->full_name,
                'department' => $user->department->name ?? 'N/A',
                'days_present' => $dtrs->count(),
                'total_hours' => $dtrs->sum('hours_worked'),
                'overtime_hours' => $dtrs->sum('overtime_hours'),
                'attendance_rate' => $this->calculateAttendanceRate($user->id, $year, $month)
            ];

            $report['total_days'] += $userStats['days_present'];
            $report['total_hours'] += $userStats['total_hours'];
            $report['total_overtime'] += $userStats['overtime_hours'];

            // Group by department
            $dept = $userStats['department'];
            if (!isset($report['departments'][$dept])) {
                $report['departments'][$dept] = [
                    'employees' => 0,
                    'total_hours' => 0,
                    'total_overtime' => 0,
                    'avg_attendance_rate' => 0
                ];
            }
            $report['departments'][$dept]['employees']++;
            $report['departments'][$dept]['total_hours'] += $userStats['total_hours'];
            $report['departments'][$dept]['total_overtime'] += $userStats['overtime_hours'];
        }

        // Calculate averages
        $workDays = Carbon::create($year, $month, 1)->daysInMonth;
        $report['attendance_rate'] = $report['total_employees'] > 0 
            ? ($report['total_days'] / ($report['total_employees'] * $workDays)) * 100 
            : 0;

        foreach ($report['departments'] as $dept => &$data) {
            $data['avg_attendance_rate'] = $data['employees'] > 0 
                ? ($data['total_hours'] / ($data['employees'] * 8 * $workDays)) * 100 
                : 0;
        }

        return $report;
    }

    /**
     * Calculate attendance rate for a user.
     */
    private function calculateAttendanceRate(int $userId, int $year, int $month): float
    {
        $totalWorkDays = Carbon::create($year, $month, 1)->daysInMonth;
        $presentDays = DTR::forUser($userId)
            ->forMonth($year, $month)
            ->approved()
            ->count();
        
        return $totalWorkDays > 0 ? ($presentDays / $totalWorkDays) * 100 : 0;
    }
}
