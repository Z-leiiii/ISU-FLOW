<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DTR;
use App\Models\LeaveBalance;
use App\Models\LeaveApplication;
use App\Models\Notification;
use App\Services\LeaveCreditService;
use App\Services\DTRService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get statistics
        $stats = [
            'total_employees' => User::whereHas('roles', function($q) {
                $q->where('name', 'faculty');
            })->count(),
            'pending_dtr' => DTR::pending()->count(),
            'pending_leave_applications' => LeaveApplication::where('status', 'pending')->count(),
            'low_balance_alerts' => LeaveBalance::lowBalance()->count(),
            'critical_balance_alerts' => LeaveBalance::criticalBalance()->count(),
        ];

        // Get recent DTR submissions needing validation
        $pendingDTRs = DTR::with(['user'])
            ->pending()
            ->orWhere('status', 'submitted')
            ->orderBy('date', 'desc')
            ->take(10)
            ->get();

        // Get pending leave applications
        $pendingApplications = LeaveApplication::with(['user', 'leaveType'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Get low balance alerts
        $lowBalanceAlerts = LeaveBalance::with(['user', 'leaveType'])
            ->lowBalance()
            ->orderBy('current_balance', 'asc')
            ->take(10)
            ->get();

        // Get monthly DTR submission trends
        $dtrTrends = $this->getDTRSubmissionTrends();

        // Get leave balance distribution
        $balanceDistribution = $this->getLeaveBalanceDistribution();

        // Get department statistics
        $departmentStats = $this->getDepartmentStatistics();

        return view('hr.dashboard', compact(
            'stats',
            'pendingDTRs',
            'pendingApplications',
            'lowBalanceAlerts',
            'dtrTrends',
            'balanceDistribution',
            'departmentStats'
        ));
    }

    public function validateDTR(Request $request, $dtrId)
    {
        $dtr = DTR::findOrFail($dtrId);
        
        $request->validate([
            'action' => 'required|in:approve,reject',
            'remarks' => 'nullable|string|max:500'
        ]);

        $dtr->status = $request->action === 'approve' ? 'approved' : 'rejected';
        $dtr->approved_by = auth()->id();
        $dtr->approved_at = now();
        $dtr->remarks = $request->remarks;
        $dtr->save();

        // Create notification for user
        $message = $request->action === 'approve' 
            ? 'Your DTR for ' . $dtr->date->format('M d, Y') . ' has been approved.'
            : 'Your DTR for ' . $dtr->date->format('M d, Y') . ' has been rejected.';
            
        Notification::create([
            'user_id' => $dtr->user_id,
            'title' => 'DTR ' . ucfirst($request->action),
            'message' => $message,
            'type' => 'dtr_' . $request->action,
            'priority' => $request->action === 'approve' ? 'info' : 'warning',
            'data' => [
                'dtr_id' => $dtr->id,
                'date' => $dtr->date,
                'action' => $request->action
            ]
        ]);

        return redirect()->back()->with('success', "DTR has been {$request->action}d successfully.");
    }

    public function bulkValidateDTR(Request $request)
    {
        $request->validate([
            'dtr_ids' => 'required|array',
            'dtr_ids.*' => 'exists:d_t_r_s,id',
            'action' => 'required|in:approve,reject'
        ]);

        $validated = DTR::whereIn('id', $request->dtr_ids)
            ->update([
                'status' => $request->action === 'approve' ? 'approved' : 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]);

        return redirect()->back()->with('success', "Bulk {$request->action} completed for {$validated} DTR records.");
    }

    public function syncMonthlyBalances(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:' . date('Y'),
            'month' => 'required|integer|min:1|max:12'
        ]);

        $year = $request->year;
        $month = $request->month;

        // Get all faculty users
        $faculty = User::whereHas('roles', function($q) {
            $q->where('name', 'faculty');
        })->get();

        $synced = 0;
        foreach ($faculty as $user) {
            // Sync DTR data
            $this->syncUserDTRData($user, $year, $month);
            
            // Recalculate leave balances
            $this->recalculateUserLeaveBalances($user, $year);
            
            $synced++;
        }

        return redirect()->back()->with('success', "Monthly synchronization completed for {$synced} employees.");
    }

    private function getDTRSubmissionTrends(): array
    {
        $trends = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->month;
            $year = $date->year;
            
            $trends[] = [
                'month' => $date->format('M Y'),
                'submitted' => DTR::whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->where('status', '!=', 'pending')
                    ->count(),
                'approved' => DTR::whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->where('status', 'approved')
                    ->count(),
                'pending' => DTR::whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->pending()
                    ->count()
            ];
        }
        
        return $trends;
    }

    private function getLeaveBalanceDistribution(): array
    {
        $distribution = [
            'good' => LeaveBalance::where('current_balance', '>=', 5)->count(),
            'moderate' => LeaveBalance::where('current_balance', '>=', 3)->where('current_balance', '<', 5)->count(),
            'low' => LeaveBalance::where('current_balance', '>=', 1)->where('current_balance', '<', 3)->count(),
            'critical' => LeaveBalance::where('current_balance', '<', 1)->count()
        ];
        
        return $distribution;
    }

    private function getDepartmentStatistics(): array
    {
        return DB::table('users')
            ->join('department_user', 'users.id', '=', 'department_user.user_id')
            ->join('departments', 'department_user.department_id', '=', 'departments.id')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'faculty')
            ->select('departments.name', DB::raw('count(*) as count'))
            ->groupBy('departments.id', 'departments.name')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
    }

    private function syncUserDTRData($user, $year, $month): void
    {
        // Get all approved DTR for the month
        $dtrs = DTR::forUser($user->id)
            ->forMonth($year, $month)
            ->approved()
            ->get();

        $totalHours = $dtrs->sum('hours_worked');
        $overtimeHours = $dtrs->sum('overtime_hours');

        // Create or update CTO balance based on overtime
        if ($overtimeHours > 0) {
            $ctoBalance = LeaveBalance::firstOrCreate([
                'user_id' => $user->id,
                'leave_type_id' => $this->getCTOLeaveTypeId(),
                'year' => $year
            ]);

            $ctoBalance->total_earned += $overtimeHours;
            $ctoBalance->updateBalance();

            // Create notification for CTO earned
            Notification::createCTOEarnedNotification($user->id, $overtimeHours);
        }
    }

    private function recalculateUserLeaveBalances($user, $year): void
    {
        $leaveTypes = \App\Models\LeaveType::all();
        
        foreach ($leaveTypes as $leaveType) {
            $computation = LeaveCreditService::computeLeaveCredits($user, $leaveType);
            
            LeaveBalance::updateOrCreate([
                'user_id' => $user->id,
                'leave_type_id' => $leaveType->id,
                'year' => $year
            ], [
                'total_earned' => $computation['total_earned'],
                'total_used' => $computation['total_used'],
                'current_balance' => $computation['balance'],
                'computed_at' => now()
            ]);

            // Check for low balance alerts
            if ($computation['balance'] < 1) {
                Notification::createCriticalBalanceNotification(
                    $user->id, 
                    $leaveType->name, 
                    $computation['balance']
                );
            } elseif ($computation['balance'] < 3) {
                Notification::createLowBalanceNotification(
                    $user->id, 
                    $leaveType->name, 
                    $computation['balance']
                );
            }
        }
    }

    private function getCTOLeaveTypeId(): int
    {
        return \App\Models\LeaveType::where('name', 'CTO')->first()->id;
    }
}
