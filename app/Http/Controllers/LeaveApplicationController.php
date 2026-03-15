<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use App\Models\User;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\LeaveCredit;
use App\Models\Notification;
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php

use App\Services\LeaveCreditService;
use App\Services\EmailNotificationService;
=======
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
use App\Services\LeaveCreditService;
use App\Services\EmailNotificationService;
use Carbon\Carbon;
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php

class LeaveApplicationController extends Controller
{
    /**
     * Employee Leave List
     */
    public function index()
    {
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
        $applications = LeaveApplication::where('user_id', Auth::id())
            ->latest()
=======
        $user = Auth::user();
        
        // Prevent HR and Admin from accessing their own leave applications page
        if ($user->hasRole('hr') || $user->hasRole('admin')) {
            return redirect()->route('hr.leave-applications.index')
                ->with('info', 'Redirected to HR Applications dashboard.');
        }
        
        $applications = LeaveApplication::where('user_id', $user->id)
            ->with(['user', 'leaveType', 'recommendedBy', 'approvedBy', 'disapprovedBy'])
            ->orderBy('created_at', 'desc')
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
            ->paginate(10);

        $leaveTypes = LeaveType::orderBy('name')->get();

        return view('leave-applications.index', compact('applications', 'leaveTypes'));
    }

    /**
     * Show Leave Application Form
     */
    public function create()
    {
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
        $leaveTypes = LeaveType::orderBy('name')->get();

        return view('leave-applications.create', compact('leaveTypes'));
=======
        $user = Auth::user();
        
        // Prevent HR and Admin from applying for leave
        if ($user->hasRole('hr') || $user->hasRole('admin')) {
            return redirect()->route('dashboard')
                ->with('error', 'HR and Admin users are not allowed to apply for leave.');
        }
        
        $leaveTypes = LeaveType::where('is_active', true)->get();
        
        // Get user's current leave balances
        $leaveBalances = LeaveCredit::where('user_id', $user->id)
            ->with('leaveType')
            ->where('as_of_date', function($query) {
                $query->selectRaw('MAX(as_of_date)')
                    ->from('leave_credits')
                    ->whereColumn('user_id', 'leave_credits.user_id')
                    ->whereColumn('leave_type_id', 'leave_credits.leave_type_id');
            })
            ->get();

        return view('leave-applications.create', compact('leaveTypes', 'leaveBalances'));
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
    }

    /**
     * Store Leave Application
     */
    public function store(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'attachment' => 'nullable|file|max:2048'
        ]);

<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
        $days = Carbon::parse($request->start_date)
            ->diffInDays(Carbon::parse($request->end_date)) + 1;

        $leaveCredit = LeaveCreditService::getUserLeaveCredit(
            Auth::id(),
            $request->leave_type_id
        );

        if (!$leaveCredit || $leaveCredit->balance < $days) {
            return back()->with('error', 'Insufficient leave balance.');
        }

        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')
                ->store('leave_attachments', 'public');
        }

=======
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
        $leaveType = LeaveType::findOrFail($request->leave_type_id);
        
        // Calculate total days
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $numberOfDays = $startDate->diffInDaysFiltered(function ($date) {
            return !$date->isWeekend();
        }, $endDate) + 1;
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php

        // Check leave balance using new computation service
        $validation = LeaveCreditService::validateLeaveBalance($user->id, $request->leave_type_id, $numberOfDays);
        
        if (!$validation['valid']) {
            // Send insufficient balance email notification
            EmailNotificationService::sendInsufficientBalanceNotification(
                $user,
                $leaveType,
                $numberOfDays,
                $validation['balance']
            );
            
            return back()->with('error', $validation['message']);
        }

<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
=======

        // Check leave balance using new computation service
        $validation = LeaveCreditService::validateLeaveBalance($user->id, $request->leave_type_id, $numberOfDays);
        
        if (!$validation['valid']) {
            // Send insufficient balance email notification
            EmailNotificationService::sendInsufficientBalanceNotification(
                $user,
                $leaveType,
                $numberOfDays,
                $validation['balance']
            );
            
            return back()->with('error', $validation['message']);
        }

>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
        $isWithoutPay = false;
        $warningMessage = '';

=======

        // Check leave balance using new computation service
        $validation = LeaveCreditService::validateLeaveBalance($user->id, $request->leave_type_id, $numberOfDays);
        
        if (!$validation['valid']) {
            // Send insufficient balance email notification
            EmailNotificationService::sendInsufficientBalanceNotification(
                $user,
                $leaveType,
                $numberOfDays,
                $validation['balance']
            );
            
            return back()->with('error', $validation['message']);
        }

        $isWithoutPay = false;
        $warningMessage = '';

>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
        $isWithoutPay = false;
        $warningMessage = '';

>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
        $isWithoutPay = false;
        $warningMessage = '';

>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
        // Check if leave should be without pay
        if (!$leaveType->is_paid) {
            $isWithoutPay = true;
        } elseif ($validation['balance'] < $numberOfDays) {
            $isWithoutPay = true;
            $warningMessage = "You have insufficient {$leaveType->name} balance. This application will be marked as leave without pay.";
        }

        // Handle file upload
        $documentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $documentPath = $file->storeAs('leave-documents', $fileName, 'public');
        }

        // Generate application number
        $applicationNumber = 'LA-' . date('Y') . '-' . str_pad(LeaveApplication::count() + 1, 6, '0', STR_PAD_LEFT);

        // Create leave application
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
        $application = LeaveApplication::create([
            'user_id' => Auth::id(),
            'leave_type_id' => $request->leave_type_id,
            'application_number' => $applicationNumber,
            'date_filed' => now(),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
            'days' => $days,
            'reason' => $request->reason,
            'attachment' => $attachmentPath,
            'status' => 'Pending'
        ]);

        EmailNotificationService::sendNewApplicationNotification($application);

        return redirect()
            ->route('leave-applications.index')
            ->with('success', 'Leave application submitted successfully.');
=======
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
            'number_of_days' => $numberOfDays,
            'reason' => $request->reason,
            'status' => 'pending',
            'is_without_pay' => $isWithoutPay,
            'document_path' => $documentPath,
        ]);

<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
=======
            'number_of_days' => $numberOfDays,
            'reason' => $request->reason,
            'status' => 'pending',
            'is_without_pay' => $isWithoutPay,
            'document_path' => $documentPath,
        ]);

>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
        // Create notifications and send emails
        EmailNotificationService::sendNewApplicationNotification($leaveApplication);
        
        $hrUsers = User::role('hr')->get();
        foreach ($hrUsers as $hr) {
            Notification::create([
                'user_id' => $hr->id,
                'title' => 'New Leave Application',
                'message' => "{$user->full_name} has filed a {$leaveType->name} application.",
                'type' => 'leave_application',
                'data' => [
                    'application_id' => $application->id,
                    'user_id' => $user->id,
                ],
            ]);
        }
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php

=======
            'number_of_days' => $numberOfDays,
            'reason' => $request->reason,
            'status' => 'pending',
            'is_without_pay' => $isWithoutPay,
            'document_path' => $documentPath,
        ]);

        // Create notifications and send emails
        EmailNotificationService::sendNewApplicationNotification($leaveApplication);
        
        $hrUsers = User::role('hr')->get();
        foreach ($hrUsers as $hr) {
            Notification::create([
                'user_id' => $hr->id,
                'title' => 'New Leave Application',
                'message' => "{$user->full_name} has filed a {$leaveType->name} application.",
                'type' => 'leave_application',
                'data' => [
                    'application_id' => $application->id,
                    'user_id' => $user->id,
                ],
            ]);
        }

>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======

>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======

>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======

>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
        // Notify department head if applicable
        if ($user->department) {
            $departmentHead = User::where('department_id', $user->department_id)
                ->whereHas('roles', function($query) {
                    $query->where('name', 'department_head');
                })
                ->first();

            if ($departmentHead) {
                Notification::create([
                    'user_id' => $departmentHead->id,
                    'title' => 'New Leave Application',
                    'message' => "{$user->full_name} has filed a {$leaveType->name} application.",
                    'type' => 'leave_application',
                    'data' => [
                        'application_id' => $application->id,
                        'user_id' => $user->id,
                    ],
                ]);
            }
        }

        $message = $warningMessage ? $warningMessage : 'Leave application submitted successfully.';
        
        return redirect()->route('leave-applications.show', $application)
            ->with('success', $message);
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
    }

    /**
     * Show Leave Details
     */
    public function show(LeaveApplication $leaveApplication)
    {
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
=======
        $this->authorize('view', $leaveApplication);
        
        $leaveApplication->load(['user', 'leaveType', 'recommendedBy', 'approvedBy', 'disapprovedBy']);
        
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
        return view('leave-applications.show', compact('leaveApplication'));
    }

    /**
     * Admin Leave List
     */
    public function adminIndex(Request $request)
    {
        $query = LeaveApplication::with(['user', 'leaveType'])
            ->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
        if ($request->leave_type_id) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        $applications = $query->paginate(15);
        $leaveTypes = LeaveType::orderBy('name')->get();

        return view('leave-applications.admin-index', compact('applications', 'leaveTypes'));
=======
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|min:10',
        ]);

        // Calculate total days
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $numberOfDays = $startDate->diffInDaysFiltered(function ($date) {
            return !$date->isWeekend();
        }, $endDate) + 1;

        // Handle file upload
        if ($request->hasFile('attachment')) {
            // Delete old file
            if ($leaveApplication->document_path) {
                Storage::disk('public')->delete($leaveApplication->document_path);
            }
            
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $documentPath = $file->storeAs('leave-documents', $fileName, 'public');
            
            $leaveApplication->document_path = $documentPath;
        }

        $leaveApplication->update([
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'number_of_days' => $numberOfDays,
            'reason' => $request->reason,
        ]);

        return redirect()->route('leave-applications.show', $leaveApplication)
            ->with('success', 'Leave application updated successfully.');
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
    }

    /**
     * Approve Leave
     */
    public function approve($id)
    {
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
        $application = LeaveApplication::findOrFail($id);

        if ($application->status !== 'Pending') {
            return back()->with('error', 'Application already processed.');
        }

        LeaveCreditService::deductLeave(
            $application->user_id,
            $application->leave_type_id,
            $application->days
        );

        $application->update([
            'status' => 'Approved',
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        EmailNotificationService::sendLeaveApprovedNotification($application);

        return back()->with('success', 'Leave application approved.');
    }

    /**
     * Reject Leave
=======
        $this->authorize('delete', $leaveApplication);
        
        if ($leaveApplication->status !== 'pending') {
            return back()->with('error', 'Cannot delete application that is already processed.');
        }

        // Delete file if exists
        if ($leaveApplication->document_path) {
            Storage::disk('public')->delete($leaveApplication->document_path);
        }
        
        $leaveApplication->delete();

        return redirect()->route('leave-applications.index')
            ->with('success', 'Leave application deleted successfully.');
    }

    /**
     * Display admin index of all leave applications.
     */
    public function adminIndex()
    {
        $user = Auth::user();
        
        if (!$user->hasRole('admin') && !$user->hasRole('hr')) {
            abort(403, 'Unauthorized action.');
        }
        
        $applications = LeaveApplication::with(['user', 'leaveType'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $leaveTypes = LeaveType::orderBy('name')->get();
        
        return view('leave-applications.admin-index', compact('applications', 'leaveTypes'));
    }

    /**
     * Approve a leave application.
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
     */
    public function reject(Request $request, $id)
    {
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
        $request->validate([
            'remarks' => 'required|string|max:500'
        ]);

        $application = LeaveApplication::findOrFail($id);

        if ($application->status !== 'Pending') {
            return back()->with('error', 'Application already processed.');
        }

        $application->update([
            'status' => 'Rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'remarks' => $request->remarks
        ]);

        EmailNotificationService::sendLeaveRejectedNotification($application);

        return back()->with('success', 'Leave application rejected.');
    }

    /**
     * Download Attachment
=======
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
        $this->authorize('approve', $leaveApplication);
        
        if ($leaveApplication->status !== 'pending') {
            return back()->with('error', 'Application has already been processed.');
        }
        
        $leaveApplication->update([
            'status' => 'approved',
            'approved_at' => now(),
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
=======
        $this->authorize('approve', $leaveApplication);
        
        if ($leaveApplication->status !== 'pending') {
            return back()->with('error', 'Application has already been processed.');
        }
        
        $leaveApplication->update([
            'status' => 'approved',
            'approved_at' => now(),
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
            'approved_by' => Auth::id(),
            'hr_remarks' => $request->hr_remarks,
        ]);
        
        // Create notification and send email
        Notification::create([
            'user_id' => $leaveApplication->user_id,
            'title' => 'Leave Application Approved',
            'message' => 'Your leave application has been approved.',
            'type' => 'leave_approval',
            'data' => [
                'application_id' => $leaveApplication->id,
            ],
        ]);
        
        // Send email notification
        EmailNotificationService::sendApplicationStatusNotification(
            $leaveApplication,
            'approved',
            $request->hr_remarks,
            Auth::user()
        );
        
        return back()->with('success', 'Leave application approved successfully.');
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
    }

    /**
     * Reject a leave application.
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
     */
    public function downloadAttachment($id)
    {
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
        $application = LeaveApplication::findOrFail($id);

        if (!$application->attachment) {
            return back()->with('error', 'No attachment found.');
        }

        return Storage::disk('public')->download($application->attachment);
    }

    /**
     * Delete Leave Application
=======
    }

    /**
     * Reject a leave application.
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
    }

    /**
     * Reject a leave application.
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
     */
    public function destroy($id)
    {
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
        $application = LeaveApplication::findOrFail($id);

        if ($application->status !== 'Pending') {
            return back()->with('error', 'Only pending applications can be deleted.');
        }

        if ($application->attachment) {
            Storage::disk('public')->delete($application->attachment);
        }

        $application->delete();

        return back()->with('success', 'Leave application deleted.');
=======
        $this->authorize('approve', $leaveApplication);
        
        if ($leaveApplication->status !== 'pending') {
            return back()->with('error', 'Application has already been processed.');
        }
        
        $request->validate([
            'hr_remarks' => 'required|string|max:500',
        ]);
        
        $leaveApplication->update([
            'status' => 'disapproved',
            'disapproved_at' => now(),
            'disapproved_by' => Auth::id(),
            'hr_remarks' => $request->hr_remarks,
        ]);
        
=======
        $this->authorize('approve', $leaveApplication);
        
        if ($leaveApplication->status !== 'pending') {
            return back()->with('error', 'Application has already been processed.');
        }
        
        $request->validate([
            'hr_remarks' => 'required|string|max:500',
        ]);
        
        $leaveApplication->update([
            'status' => 'disapproved',
            'disapproved_at' => now(),
            'disapproved_by' => Auth::id(),
            'hr_remarks' => $request->hr_remarks,
        ]);
        
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
        $this->authorize('approve', $leaveApplication);
        
        if ($leaveApplication->status !== 'pending') {
            return back()->with('error', 'Application has already been processed.');
        }
        
        $request->validate([
            'hr_remarks' => 'required|string|max:500',
        ]);
        
        $leaveApplication->update([
            'status' => 'disapproved',
            'disapproved_at' => now(),
            'disapproved_by' => Auth::id(),
            'hr_remarks' => $request->hr_remarks,
        ]);
        
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
        $this->authorize('approve', $leaveApplication);
        
        if ($leaveApplication->status !== 'pending') {
            return back()->with('error', 'Application has already been processed.');
        }
        
        $request->validate([
            'hr_remarks' => 'required|string|max:500',
        ]);
        
        $leaveApplication->update([
            'status' => 'disapproved',
            'disapproved_at' => now(),
            'disapproved_by' => Auth::id(),
            'hr_remarks' => $request->hr_remarks,
        ]);
        
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
        // Create notification and send email
        Notification::create([
            'user_id' => $leaveApplication->user_id,
            'title' => 'Leave Application Disapproved',
            'message' => 'Your leave application has been disapproved.',
            'type' => 'leave_approval',
            'data' => [
                'application_id' => $leaveApplication->id,
            ],
        ]);
        
        // Send email notification
        EmailNotificationService::sendApplicationStatusNotification(
            $leaveApplication,
            'rejected',
            $request->hr_remarks,
            Auth::user()
        );
        
        return back()->with('success', 'Leave application disapproved successfully.');
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
<<<<<<< C:/ISU-FLOW/systemF/app/Http/Controllers/LeaveApplicationController.php
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
        $this->authorize('approve', $leaveApplication);
        
        if ($leaveApplication->status !== 'pending') {
            return back()->with('error', 'Application has already been processed.');
        }
        
        $request->validate([
            'hr_remarks' => 'required|string|max:500',
        ]);
        
        $leaveApplication->update([
            'status' => 'disapproved',
            'disapproved_at' => now(),
            'disapproved_by' => Auth::id(),
            'hr_remarks' => $request->hr_remarks,
        ]);
        
        // Create notification and send email
        Notification::create([
            'user_id' => $leaveApplication->user_id,
            'title' => 'Leave Application Disapproved',
            'message' => 'Your leave application has been disapproved.',
            'type' => 'leave_approval',
            'data' => [
                'application_id' => $leaveApplication->id,
            ],
        ]);
        
        // Send email notification
        EmailNotificationService::sendApplicationStatusNotification(
            $leaveApplication,
            'rejected',
            $request->hr_remarks,
            Auth::user()
        );
        
        return back()->with('success', 'Leave application disapproved successfully.');
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Http/Controllers/LeaveApplicationController.php
    }
}
