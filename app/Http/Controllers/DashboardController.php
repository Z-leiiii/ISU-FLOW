<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\LeaveApplication;
use App\Models\LeaveCredit;
use App\Models\LeaveType;
use App\Models\DesignationDocument;
use App\Models\Notification;
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
=======
use App\Services\LeaveCreditService;
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
use App\Services\LeaveCreditService;
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
use App\Services\LeaveCreditService;
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
use App\Services\LeaveCreditService;
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
use App\Services\LeaveCreditService;
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->hasRole('admin') || $user->hasRole('hr')) {
            return $this->hrDashboard();
        }
        
        return $this->employeeDashboard();
    }

    /**
     * Employee dashboard with leave balance monitoring
     */
    private function employeeDashboard()
    {
        $user = Auth::user();
        
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
        // Get leave balances
        $leaveBalances = LeaveCredit::where('user_id', $user->id)
            ->with('leaveType')
            ->where('as_of_date', function($query) {
                $query->selectRaw('MAX(as_of_date)')
                    ->from('leave_credits')
                    ->whereColumn('user_id', 'leave_credits.user_id')
                    ->whereColumn('leave_type_id', 'leave_credits.leave_type_id');
            })
=======
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
        // Get leave balances with new computation
        $leaveBalances = LeaveCredit::where('user_id', $user->id)
            ->with('leaveType')
            ->get();

        // Update leave credits using new computation
        LeaveCreditService::updateUserAllLeaveCredits($user->id);
        
        // Get updated leave balances
        $leaveBalances = LeaveCredit::where('user_id', $user->id)
            ->with('leaveType')
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
            ->get();

        // Get recent leave applications
        $recentApplications = LeaveApplication::where('user_id', $user->id)
            ->with('leaveType')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get pending designation documents
        $pendingDesignations = DesignationDocument::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        // Get unread notifications
        $unreadNotifications = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Check for leave without pay warnings
        $leaveWithoutPayWarnings = $this->checkLeaveWithoutPayWarnings($user);

        // Get current designation
        $currentDesignation = $user->designation;

        return view('dashboard.employee', compact(
            'user',
            'leaveBalances',
            'recentApplications',
            'pendingDesignations',
            'unreadNotifications',
            'leaveWithoutPayWarnings',
            'currentDesignation'
        ));
    }

    /**
     * HR/Admin dashboard
     */
    private function hrDashboard()
    {
        $user = Auth::user();
        
        // Get statistics
        $totalApplications = LeaveApplication::count();
        $pendingApplications = LeaveApplication::where('status', 'pending')->count();
        $approvedApplications = LeaveApplication::where('status', 'approved')->count();
        $disapprovedApplications = LeaveApplication::where('status', 'disapproved')->count();

        // Get recent applications
        $recentApplications = LeaveApplication::with(['user', 'leaveType'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get pending designation documents
        $pendingDesignations = DesignationDocument::where('status', 'pending')
            ->with(['user', 'designation'])
            ->count();

        // Get leave without pay applications
        $leaveWithoutPayApplications = LeaveApplication::where('is_without_pay', true)
            ->where('status', 'pending')
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/DashboardController.php
            ->count();
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php
=======
            ->with(['user', 'leaveType'])
            ->limit(10)
            ->get();
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/DashboardController.php

        return view('dashboard.hr', compact(
            'user',
            'totalApplications',
            'pendingApplications',
            'approvedApplications',
            'disapprovedApplications',
            'recentApplications',
            'pendingDesignations',
            'leaveWithoutPayApplications'
        ));
    }

    /**
     * Check for leave without pay warnings
     */
    private function checkLeaveWithoutPayWarnings($user)
    {
        $warnings = [];
        
        // Check if user has no current designation
        if (!$user->designation) {
            $warnings[] = 'No designation on record. Please upload your designation document to earn leave credits.';
        }

        // Check leave balances
        $leaveBalances = LeaveCredit::where('user_id', $user->id)
            ->with('leaveType')
            ->where('as_of_date', function($query) {
                $query->selectRaw('MAX(as_of_date)')
                    ->from('leave_credits')
                    ->whereColumn('user_id', 'leave_credits.user_id')
                    ->whereColumn('leave_type_id', 'leave_credits.leave_type_id');
            })
            ->get();

        foreach ($leaveBalances as $balance) {
            if ($balance->balance <= 0 && $balance->leaveType->is_paid) {
                $warnings[] = "You have no {$balance->leaveType->name} balance. Future applications will be marked as leave without pay.";
            } elseif ($balance->balance <= 1) {
                $warnings[] = "You have low {$balance->leaveType->name} balance ({$balance->balance} days remaining).";
            }
        }

        return $warnings;
    }

    /**
     * Get leave balance details
     */
    public function leaveBalances()
    {
        $user = Auth::user();
        
        $leaveBalances = LeaveCredit::where('user_id', $user->id)
            ->with('leaveType')
            ->where('as_of_date', function($query) {
                $query->selectRaw('MAX(as_of_date)')
                    ->from('leave_credits')
                    ->whereColumn('user_id', 'leave_credits.user_id')
                    ->whereColumn('leave_type_id', 'leave_credits.leave_type_id');
            })
            ->get();

        return view('dashboard.leave-balances', compact('user', 'leaveBalances'));
    }

    /**
     * Get leave history
     */
    public function leaveHistory()
    {
        $user = Auth::user();
        
        $applications = LeaveApplication::where('user_id', $user->id)
            ->with('leaveType')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('dashboard.leave-history', compact('user', 'applications'));
    }
}
