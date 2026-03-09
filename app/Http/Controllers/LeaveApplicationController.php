<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\LeaveCredit;
use App\Models\Notification;
use App\Services\LeaveCreditService;
use App\Services\EmailNotificationService;
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
            return redirect()->route('hr.leave-applications.index')
                ->with('info', 'Redirected to HR Applications dashboard.');
        }
        
        $applications = LeaveApplication::where('user_id', $user->id)
            ->with(['user', 'leaveType', 'recommendedBy', 'approvedBy', 'disapprovedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $leaveTypes = LeaveType::orderBy('name')->get();

        return view('leave-applications.index', compact('applications', 'leaveTypes'));
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

        $leaveType = LeaveType::findOrFail($request->leave_type_id);
        
        // Calculate total days
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $numberOfDays = $startDate->diffInDaysFiltered(function ($date) {
            return !$date->isWeekend();
        }, $endDate) + 1;

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
        $application = LeaveApplication::create([
            'user_id' => $user->id,
            'leave_type_id' => $request->leave_type_id,
            'application_number' => $applicationNumber,
            'date_filed' => now(),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
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
    }

    /**
     * Display the specified leave application.
     */
    public function show(LeaveApplication $leaveApplication)
    {
        $this->authorize('view', $leaveApplication);
        
        $leaveApplication->load(['user', 'leaveType', 'recommendedBy', 'approvedBy', 'disapprovedBy']);
        
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
    }

    /**
     * Remove the specified leave application.
     */
    public function destroy(LeaveApplication $leaveApplication)
    {
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
     */
    public function approve(Request $request, LeaveApplication $leaveApplication)
    {
        $this->authorize('approve', $leaveApplication);
        
        if ($leaveApplication->status !== 'pending') {
            return back()->with('error', 'Application has already been processed.');
        }
        
        $leaveApplication->update([
            'status' => 'approved',
            'approved_at' => now(),
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
    }

    /**
     * Reject a leave application.
     */
    public function reject(Request $request, LeaveApplication $leaveApplication)
    {
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
    }
}
