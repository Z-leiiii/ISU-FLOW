<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveApplication;
use App\Models\AttendanceRecord;
use App\Models\LeaveCredit;
use App\Models\User;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display reports index.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Check if user has HR or Admin role
        if (!$user->hasRole('admin') && !$user->hasRole('hr')) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('reports.index');
    }

    /**
     * Generate leave summary report.
     */
    public function leaveSummary(Request $request)
    {
        $user = Auth::user();
        
        // Check if user has HR or Admin role
        if (!$user->hasRole('admin') && !$user->hasRole('hr')) {
            abort(403, 'Unauthorized action.');
        }
        
        $year = $request->get('year', now()->year);
        $departmentId = $request->get('department_id');
        
        $query = LeaveApplication::with(['user', 'leaveType'])
            ->whereYear('created_at', $year);

        // Filter by department if user is not admin or if department is specified
        if (!$user->hasRole('admin') || $departmentId) {
            $query->whereHas('user', function($q) use ($departmentId, $user) {
                if ($departmentId) {
                    $q->where('department_id', $departmentId);
                } elseif (!$user->hasRole('admin')) {
                    $q->where('department_id', $user->department_id);
                }
            });
        }

        $applications = $query->orderBy('created_at', 'desc')->get();

        // Calculate statistics
        $stats = [
            'total_applications' => $applications->count(),
            'approved' => $applications->where('status', 'approved')->count(),
            'rejected' => $applications->where('status', 'rejected')->count(),
            'pending' => $applications->where('status', 'pending')->count(),
            'total_days_taken' => $applications->where('status', 'approved')->sum('total_days'),
        ];

        // Group by leave type
        $byLeaveType = $applications->where('status', 'approved')
            ->groupBy('leave_type_id')
            ->map(function($group) {
                return [
                    'leave_type' => $group->first()->leaveType->name,
                    'count' => $group->count(),
                    'total_days' => $group->sum('total_days'),
                ];
            });

        // Group by month
        $byMonth = $applications->where('status', 'approved')
            ->groupBy(function($app) {
                return $app->start_date->format('F');
            })
            ->map(function($group) {
                return [
                    'count' => $group->count(),
                    'total_days' => $group->sum('total_days'),
                ];
            });

        return view('reports.leave-summary', compact('applications', 'stats', 'byLeaveType', 'byMonth', 'year'));
    }

    /**
     * Generate attendance report.
     */
    public function attendanceReport(Request $request)
    {
        $user = Auth::user();
        
        // Check if user has HR or Admin role
        if (!$user->hasRole('admin') && !$user->hasRole('hr')) {
            abort(403, 'Unauthorized action.');
        }
        
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $departmentId = $request->get('department_id');
        
        $query = AttendanceRecord::with('user')
            ->whereMonth('date', $month)
            ->whereYear('date', $year);

        // Filter by department if user is not admin or if department is specified
        if (!$user->hasRole('admin') || $departmentId) {
            $query->whereHas('user', function($q) use ($departmentId, $user) {
                if ($departmentId) {
                    $q->where('department_id', $departmentId);
                } elseif (!$user->hasRole('admin')) {
                    $q->where('department_id', $user->department_id);
                }
            });
        }

        $attendances = $query->orderBy('date', 'desc')->get();

        // Calculate statistics
        $stats = [
            'total_records' => $attendances->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'half_day' => $attendances->where('status', 'half_day')->count(),
            'total_hours' => $attendances->sum('hours_worked'),
        ];

        // Group by employee
        $byEmployee = $attendances->groupBy('user_id')
            ->map(function($group) {
                $employee = $group->first()->user;
                return [
                    'employee_name' => $employee->full_name,
                    'total_days' => $group->count(),
                    'present_days' => $group->where('status', 'present')->count(),
                    'late_days' => $group->where('status', 'late')->count(),
                    'absent_days' => $group->where('status', 'absent')->count(),
                    'total_hours' => $group->sum('hours_worked'),
                ];
            });

        return view('reports.attendance-report', compact('attendances', 'stats', 'byEmployee', 'month', 'year'));
    }

    /**
     * Generate leave credits report.
     */
    public function leaveCreditsReport(Request $request)
    {
        $user = Auth::user();
        
        // Check if user has HR or Admin role
        if (!$user->hasRole('admin') && !$user->hasRole('hr')) {
            abort(403, 'Unauthorized action.');
        }
        
        $year = $request->get('year', now()->year);
        $departmentId = $request->get('department_id');
        
        $query = LeaveCredit::with(['user', 'leaveType'])
            ->where('year', $year);

        // Filter by department if user is not admin or if department is specified
        if (!$user->hasRole('admin') || $departmentId) {
            $query->whereHas('user', function($q) use ($departmentId, $user) {
                if ($departmentId) {
                    $q->where('department_id', $departmentId);
                } elseif (!$user->hasRole('admin')) {
                    $q->where('department_id', $user->department_id);
                }
            });
        }

        $credits = $query->get();

        // Calculate statistics
        $stats = [
            'total_employees' => $credits->groupBy('user_id')->count(),
            'total_credits_earned' => $credits->sum('credits_earned'),
            'total_credits_used' => $credits->sum('credits_used'),
            'total_credits_balance' => $credits->sum('credits_balance'),
        ];

        // Group by leave type
        $byLeaveType = $credits->groupBy('leave_type_id')
            ->map(function($group) {
                return [
                    'leave_type' => $group->first()->leaveType->name,
                    'total_earned' => $group->sum('credits_earned'),
                    'total_used' => $group->sum('credits_used'),
                    'total_balance' => $group->sum('credits_balance'),
                ];
            });

        return view('reports.leave-credits', compact('credits', 'stats', 'byLeaveType', 'year'));
    }
}
