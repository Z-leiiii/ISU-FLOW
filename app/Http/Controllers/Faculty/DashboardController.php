<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DTR;
use App\Models\LeaveBalance;
use App\Models\LeaveApplication;
use App\Models\Notification;
use App\Models\CompensatoryTimeOff;
use App\Services\LeaveCreditService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get personal statistics
        $stats = [
            'total_leave_balance' => LeaveBalance::where('user_id', $user->id)
                ->where('year', date('Y'))
                ->sum('current_balance'),
            'pending_dtr' => DTR::forUser($user->id)->pending()->count(),
            'pending_applications' => LeaveApplication::where('user_id', $user->id)
                ->where('status', 'pending')->count(),
            'cto_balance' => $this->getCTOBalance($user->id),
            'service_credits' => $this->getServiceCredits($user->id),
            'leave_without_pay' => $this->getLeaveWithoutPayDays($user->id),
        ];

        // Get leave balances with status
        $leaveBalances = LeaveBalance::with('leaveType')
            ->where('user_id', $user->id)
            ->where('year', date('Y'))
            ->get()
            ->map(function ($balance) {
                $status = $balance->getBalanceStatus();
                $balance->status_color = $status['color'];
                $balance->status_message = $status['message'];
                $balance->percentage = $status['percentage'];
                return $balance;
            });

        // Get recent DTR submissions
        $recentDTRs = DTR::forUser($user->id)
            ->orderBy('date', 'desc')
            ->take(10)
            ->get();

        // Get pending DTR count for alerts
        $pendingDTRDays = $this->getPendingDTRDays($user->id);

        // Get recent leave applications
        $recentApplications = LeaveApplication::with('leaveType')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get CTO history
        $ctoHistory = $this->getCTOHistory($user->id);

        // Get notifications
        $notifications = Notification::where('user_id', $user->id)
            ->unread()
            ->highPriority()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get monthly attendance summary
        $attendanceSummary = $this->getMonthlyAttendanceSummary($user->id);

        // Get leave usage trends
        $leaveTrends = $this->getLeaveUsageTrends($user->id);

        return view('faculty.dashboard', compact(
            'stats',
            'leaveBalances',
            'recentDTRs',
            'pendingDTRDays',
            'recentApplications',
            'ctoHistory',
            'notifications',
            'attendanceSummary',
            'leaveTrends'
        ));
    }

    public function submitDTR(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'time_in' => 'nullable|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i',
            'break_start' => 'nullable|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i',
            'remarks' => 'nullable|string|max:500'
        ]);

        $user = auth()->user();

        // Check if DTR already exists for this date
        $existingDTR = DTR::where('user_id', $user->id)
            ->where('date', $request->date)
            ->first();

        if ($existingDTR && $existingDTR->status === 'approved') {
            return redirect()->back()->with('error', 'DTR for this date is already approved and cannot be modified.');
        }

        $dtr = $existingDTR ?? new DTR();
        $dtr->user_id = $user->id;
        $dtr->date = $request->date;
        $dtr->time_in = $request->time_in;
        $dtr->time_out = $request->time_out;
        $dtr->break_start = $request->break_start;
        $dtr->break_end = $request->break_end;
        $dtr->remarks = $request->remarks;
        $dtr->status = 'submitted';

        // Calculate hours worked
        $dtr->calculateHoursWorked();
        $dtr->save();

        return redirect()->back()->with('success', 'DTR submitted successfully.');
    }

    public function getBalanceDetails(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id'
        ]);

        $user = auth()->user();
        $leaveType = \App\Models\LeaveType::findOrFail($request->leave_type_id);
        
        $computation = LeaveCreditService::computeLeaveCredits($user, $leaveType);
        $balance = LeaveBalance::where('user_id', $user->id)
            ->where('leave_type_id', $leaveType->id)
            ->where('year', date('Y'))
            ->first();

        return response()->json([
            'computation' => $computation,
            'balance' => $balance,
            'details' => LeaveCreditService::getLeaveComputationDetails($user->id)
        ]);
    }

    public function markNotificationRead(Request $request, $notificationId)
    {
        $notification = Notification::where('user_id', auth()->id())
            ->findOrFail($notificationId);
        
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }

    private function getCTOBalance($userId): float
    {
        $ctoLeaveType = \App\Models\LeaveType::where('name', 'CTO')->first();
        if (!$ctoLeaveType) {
            return 0;
        }

        $balance = LeaveBalance::where('user_id', $userId)
            ->where('leave_type_id', $ctoLeaveType->id)
            ->where('year', date('Y'))
            ->first();

        return $balance ? $balance->current_balance : 0;
    }

    private function getServiceCredits($userId): float
    {
        $computationDetails = LeaveCreditService::getLeaveComputationDetails($userId);
        
        if ($computationDetails['computation_type'] === 'service_credits') {
            return $computationDetails['annual_rate'] ?? 0;
        }
        
        return 0;
    }

    private function getLeaveWithoutPayDays($userId): int
    {
        return LeaveApplication::where('user_id', $userId)
            ->where('status', 'approved')
            ->where('leave_type_id', function($query) {
                $query->where('name', 'Leave Without Pay');
            })
            ->whereYear('created_at', date('Y'))
            ->sum('days_requested');
    }

    private function getPendingDTRDays($userId): array
    {
        $currentMonth = Carbon::now();
        $daysInMonth = $currentMonth->daysInMonth;
        
        $pendingDays = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($currentMonth->year, $currentMonth->month, $day);
            
            // Skip weekends
            if ($date->isWeekend()) {
                continue;
            }
            
            // Skip future dates
            if ($date->isFuture()) {
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

    private function getCTOHistory($userId): array
    {
        return DTR::forUser($userId)
            ->where('overtime_hours', '>', 0)
            ->approved()
            ->orderBy('date', 'desc')
            ->take(10)
            ->get()
            ->map(function ($dtr) {
                return [
                    'date' => $dtr->date->format('M d, Y'),
                    'overtime_hours' => $dtr->formatted_overtime_hours,
                    'regular_hours' => $dtr->formatted_hours_worked,
                ];
            })
            ->toArray();
    }

    private function getMonthlyAttendanceSummary($userId): array
    {
        $currentMonth = Carbon::now();
        $dtrs = DTR::forUser($userId)
            ->forMonth($currentMonth->year, $currentMonth->month)
            ->approved()
            ->get();

        return [
            'total_days' => $dtrs->count(),
            'total_hours' => $dtrs->sum('hours_worked'),
            'total_overtime' => $dtrs->sum('overtime_hours'),
            'average_hours_per_day' => $dtrs->count() > 0 ? $dtrs->sum('hours_worked') / $dtrs->count() : 0,
            'attendance_rate' => $this->calculateAttendanceRate($userId, $currentMonth->year, $currentMonth->month)
        ];
    }

    private function getLeaveUsageTrends($userId): array
    {
        $trends = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->month;
            $year = $date->year;
            
            $applications = LeaveApplication::where('user_id', $userId)
                ->whereMonth('start_date', $month)
                ->whereYear('start_date', $year)
                ->where('status', 'approved')
                ->get();
            
            $trends[] = [
                'month' => $date->format('M Y'),
                'days_used' => $applications->sum('days_requested'),
                'applications_count' => $applications->count()
            ];
        }
        
        return $trends;
    }

    private function calculateAttendanceRate($userId, $year, $month): float
    {
        $totalWorkDays = Carbon::create($year, $month, 1)->daysInMonth;
        $presentDays = DTR::forUser($userId)
            ->forMonth($year, $month)
            ->approved()
            ->count();
        
        return $totalWorkDays > 0 ? ($presentDays / $totalWorkDays) * 100 : 0;
    }
}
