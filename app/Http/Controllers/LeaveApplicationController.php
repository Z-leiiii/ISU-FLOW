<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\LeaveCredit;
use App\Models\LeaveApproval;
use App\Models\Notification;
use App\Models\AuditLog;
use Carbon\Carbon;

class LeaveApplicationController extends Controller
{
    /**
     * Display a listing of the leave applications.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Prevent HR and Admin from accessing their own leave applications page
        if ($user->hasRole('hr') || $user->hasRole('admin')) {
            return redirect()->route('admin.leave-applications.index')
                ->with('info', 'Redirected to Employee Applications dashboard.');
        }
        
        $applications = LeaveApplication::where('user_id', $user->id)
            ->with(['user', 'leaveType', 'approvals.approver'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('leave-applications.index', compact('applications'));
    }

    /**
     * Show the form for creating a new leave application.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Prevent HR and Admin from applying for leave
        if ($user->hasRole('hr') || $user->hasRole('admin')) {
            return redirect()->route('dashboard')
                ->with('error', 'HR and Admin users are not allowed to apply for leave.');
        }
        
        $leaveTypes = LeaveType::where('is_active', true)->get();
        
        // Get user's leave credits
        $leaveCredits = $user->leaveCredits()
            ->with('leaveType')
            ->where('year', now()->year)
            ->get();

        return view('leave-applications.create', compact('leaveTypes', 'leaveCredits'));
    }

    /**
     * Store a newly created leave application.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Prevent HR and Admin from applying for leave
        if ($user->hasRole('hr') || $user->hasRole('admin')) {
            return redirect()->route('dashboard')
                ->with('error', 'HR and Admin users are not allowed to apply for leave.');
        }
        
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|min:10',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $user = Auth::user();
        $leaveType = LeaveType::findOrFail($request->leave_type_id);
        
        // Calculate total days
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $totalDays = $startDate->diffInDays($endDate) + 1;

        // Check if user has enough leave credits
        $leaveCredit = $user->leaveCredits()
            ->where('leave_type_id', $request->leave_type_id)
            ->where('year', now()->year)
            ->first();

        if (!$leaveCredit || $leaveCredit->credits_balance < $totalDays) {
            return back()->with('error', 'Insufficient leave credits for this application.');
        }

        // Handle file upload
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leave-attachments', 'public');
        }

        // Create leave application
        $application = LeaveApplication::create([
            'user_id' => $user->id,
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_days' => $totalDays,
            'reason' => $request->reason,
            'attachment_path' => $attachmentPath,
            'status' => 'pending',
        ]);

        // Create approval workflow
        $this->createApprovalWorkflow($application);

        // Create notification for HR approvers
        $this->createHRNotification($application);

        // Log the action
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'create',
            'module' => 'leave_application',
            'description' => "Leave application #{$application->id} created and forwarded to HR",
            'new_values' => $application->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('leave-applications.index')
            ->with('success', 'Leave application submitted successfully and forwarded to HR for approval.');
    }

    /**
     * Display the specified leave application.
     */
    public function show(LeaveApplication $leaveApplication)
    {
        $this->authorize('view', $leaveApplication);
        
        $leaveApplication->load(['user', 'leaveType', 'approvals.approver']);
        
        return view('leave-applications.show', compact('leaveApplication'));
    }

    /**
     * Show the form for editing the specified leave application.
     */
    public function edit(LeaveApplication $leaveApplication)
    {
        $this->authorize('update', $leaveApplication);
        
        if ($leaveApplication->status !== 'pending') {
            return back()->with('error', 'Cannot edit application that is already processed.');
        }

        $leaveTypes = LeaveType::where('is_active', true)->get();
        
        return view('leave-applications.edit', compact('leaveApplication', 'leaveTypes'));
    }

    /**
     * Update the specified leave application.
     */
    public function update(Request $request, LeaveApplication $leaveApplication)
    {
        $this->authorize('update', $leaveApplication);
        
        if ($leaveApplication->status !== 'pending') {
            return back()->with('error', 'Cannot edit application that is already processed.');
        }

        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|min:10',
        ]);

        $oldValues = $leaveApplication->toArray();
        
        // Calculate total days
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $totalDays = $startDate->diffInDays($endDate) + 1;

        $leaveApplication->update([
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_days' => $totalDays,
            'reason' => $request->reason,
        ]);

        // Log the action
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'update',
            'module' => 'leave_application',
            'description' => "Leave application #{$leaveApplication->id} updated",
            'old_values' => $oldValues,
            'new_values' => $leaveApplication->toArray(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('leave-applications.index')
            ->with('success', 'Leave application updated successfully.');
    }

    /**
     * Remove the specified leave application.
     */
    public function destroy(LeaveApplication $leaveApplication)
    {
        $this->authorize('delete', $leaveApplication);
        
        if ($leaveApplication->status !== 'pending') {
            return back()->with('error', 'Cannot cancel application that is already processed.');
        }

        $oldValues = $leaveApplication->toArray();
        
        $leaveApplication->update(['status' => 'cancelled']);

        // Log the action
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'cancel',
            'module' => 'leave_application',
            'description' => "Leave application #{$leaveApplication->id} cancelled",
            'old_values' => $oldValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('leave-applications.index')
            ->with('success', 'Leave application cancelled successfully.');
    }

    /**
     * Display leave applications for HR/Admin approval.
     */
    public function adminIndex()
    {
        $user = Auth::user();
        
        // Check if user has HR or Admin role
        if (!$user->hasRole('admin') && !$user->hasRole('hr')) {
            abort(403, 'Unauthorized action.');
        }

        $applications = LeaveApplication::with(['user.department', 'leaveType', 'approvals.approver'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('leave-applications.admin-index', compact('applications'));
    }

    /**
     * Approve a leave application (HR/Admin only).
     */
    public function approve(Request $request, LeaveApplication $leaveApplication)
    {
        $user = Auth::user();
        
        // Check if user has HR or Admin role
        if (!$user->hasRole('admin') && !$user->hasRole('hr')) {
            abort(403, 'Unauthorized action.');
        }

        if ($leaveApplication->status !== 'pending') {
            return back()->with('error', 'This application has already been processed.');
        }

        // Check leave balance
        $leaveBalance = LeaveBalance::where('user_id', $leaveApplication->user_id)
            ->where('leave_type_id', $leaveApplication->leave_type_id)
            ->where('year', now()->year)
            ->first();

        if (!$leaveBalance || $leaveBalance->current_balance < $leaveApplication->total_days) {
            return back()->with('error', 'Insufficient leave balance for this application.');
        }

        // Update application status
        $leaveApplication->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $user->id,
            'remarks' => $request->remarks ?? 'Approved by ' . $user->full_name,
        ]);

        // Update leave balance
        if ($leaveBalance) {
            $leaveBalance->total_used += $leaveApplication->total_days;
            $leaveBalance->current_balance -= $leaveApplication->total_days;
            $leaveBalance->save();
        }

        // Create approval record
        LeaveApproval::create([
            'leave_application_id' => $leaveApplication->id,
            'approver_id' => $user->id,
            'level' => 'hr',
            'status' => 'approved',
            'remarks' => $request->remarks,
            'approved_at' => now(),
        ]);

        // Create notification for employee
        Notification::create([
            'user_id' => $leaveApplication->user_id,
            'title' => 'Leave Application Approved',
            'message' => "Your leave application from {$leaveApplication->start_date->format('M d, Y')} to {$leaveApplication->end_date->format('M d, Y')} has been approved.",
            'type' => 'success',
        ]);

        // Log the action
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'approve',
            'module' => 'leave_application',
            'description' => "Leave application #{$leaveApplication->id} approved for {$leaveApplication->user->full_name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.leave-applications.index')
            ->with('success', "Leave application approved successfully for {$leaveApplication->user->full_name}.");
    }

    /**
     * Reject a leave application (HR/Admin only).
     */
    public function reject(Request $request, LeaveApplication $leaveApplication)
    {
        $user = Auth::user();
        
        // Check if user has HR or Admin role
        if (!$user->hasRole('admin') && !$user->hasRole('hr')) {
            abort(403, 'Unauthorized action.');
        }

        if ($leaveApplication->status !== 'pending') {
            return back()->with('error', 'This application has already been processed.');
        }

        // Update application status
        $leaveApplication->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejected_by' => $user->id,
            'remarks' => $request->remarks ?? 'Rejected by ' . $user->full_name,
        ]);

        // Create approval record
        LeaveApproval::create([
            'leave_application_id' => $leaveApplication->id,
            'approver_id' => $user->id,
            'level' => 'hr',
            'status' => 'rejected',
            'remarks' => $request->remarks,
            'rejected_at' => now(),
        ]);

        // Create notification for employee
        Notification::create([
            'user_id' => $leaveApplication->user_id,
            'title' => 'Leave Application Rejected',
            'message' => "Your leave application from {$leaveApplication->start_date->format('M d, Y')} to {$leaveApplication->end_date->format('M d, Y')} has been rejected. Reason: " . ($request->remarks ?? 'No reason provided'),
            'type' => 'error',
        ]);

        // Log the action
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'reject',
            'module' => 'leave_application',
            'description' => "Leave application #{$leaveApplication->id} rejected for {$leaveApplication->user->full_name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.leave-applications.index')
            ->with('success', "Leave application rejected successfully for {$leaveApplication->user->full_name}.");
    }

    /**
     * Create notification for HR approvers
     */
    private function createHRNotification(LeaveApplication $application)
    {
        // Get all HR users
        $hrUsers = User::role('hr')->get();
        
        foreach ($hrUsers as $hrUser) {
            Notification::create([
                'user_id' => $hrUser->id,
                'title' => 'New Leave Application',
                'message' => "New leave application from {$application->user->full_name} for {$application->leaveType->name} ({$application->total_days} days) requires your review.",
                'type' => 'info',
                'module' => 'leave_application',
                'module_id' => $application->id,
            ]);
        }
    }

    /**
     * Create notification for approvers
     */
    private function createNotification(LeaveApplication $application)
    {
        $approvers = [$application->user->department->head_of_department ?? 1];
        
        foreach ($approvers as $approverId) {
            Notification::create([
                'user_id' => $approverId,
                'title' => 'New Leave Application',
                'message' => "Leave application submitted for {$application->user->full_name}",
                'type' => 'info',
            ]);
        }
    }
}
