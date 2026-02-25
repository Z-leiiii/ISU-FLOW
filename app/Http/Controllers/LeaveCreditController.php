<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveCredit;
use App\Models\LeaveBalance;
use App\Models\User;
use App\Models\LeaveType;

class LeaveCreditController extends Controller
{
    /**
     * Display leave credits for the authenticated user (employees) or all users (HR/Admin).
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->hasRole('admin') || $user->hasRole('hr')) {
            // HR and Admin can see all employees' leave credits
            $employees = User::with(['department', 'leaveBalances.leaveType'])
                ->where('is_active', true)
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get();
            
            return view('leave-credits.hr-index', compact('employees'));
        } else {
            // Employees can only see their own leave credits
            $leaveCredits = $user->leaveCredits()
                ->with('leaveType')
                ->where('year', now()->year)
                ->get();

            return view('leave-credits.index', compact('leaveCredits'));
        }
    }

    /**
     * Show the form for creating a new leave credit (HR/Admin only).
     */
    public function create()
    {
        $user = Auth::user();
        
        // Check if user has HR or Admin role
        if (!$user->hasRole('admin') && !$user->hasRole('hr')) {
            abort(403, 'Unauthorized action.');
        }
        
        $leaveTypes = LeaveType::where('is_active', true)->get();
        $employees = User::where('is_active', true)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('leave-credits.create', compact('leaveTypes', 'employees'));
    }

    /**
     * Store a newly created leave credit (HR/Admin only).
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Check if user has HR or Admin role
        if (!$user->hasRole('admin') && !$user->hasRole('hr')) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'credits_to_add' => 'required|integer|min:1|max:365',
            'remarks' => 'nullable|string|max:255',
        ]);

        $employee = User::findOrFail($request->user_id);
        $leaveType = LeaveType::findOrFail($request->leave_type_id);
        $currentYear = now()->year;

        // Find or create leave balance
        $leaveBalance = LeaveBalance::firstOrCreate(
            [
                'user_id' => $request->user_id,
                'leave_type_id' => $request->leave_type_id,
                'year' => $currentYear,
            ],
            [
                'total_earned' => 0,
                'total_used' => 0,
                'current_balance' => 0,
                'carry_over' => 0,
            ]
        );

        // Update leave balance
        $leaveBalance->total_earned += $request->credits_to_add;
        $leaveBalance->current_balance += $request->credits_to_add;
        $leaveBalance->save();

        // Create leave credit record
        LeaveCredit::create([
            'user_id' => $request->user_id,
            'leave_type_id' => $request->leave_type_id,
            'credits_earned' => $request->credits_to_add,
            'credits_used' => 0,
            'credits_balance' => $leaveBalance->current_balance,
            'year' => $currentYear,
            'remarks' => $request->remarks ?? "Leave credits added by {$user->full_name}",
        ]);

        return redirect()->route('leave-credits.index')
            ->with('success', "Successfully added {$request->credits_to_add} {$leaveType->name} credits to {$employee->full_name}.");
    }

    /**
     * Update leave balance for an employee (HR/Admin only).
     */
    public function updateLeaveBalance(Request $request, $userId, $leaveTypeId)
    {
        $user = Auth::user();
        
        // Check if user has HR or Admin role
        if (!$user->hasRole('admin') && !$user->hasRole('hr')) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'credits_to_add' => 'required|integer|min:1|max:365',
            'remarks' => 'nullable|string|max:255',
        ]);

        $employee = User::findOrFail($userId);
        $leaveType = LeaveType::findOrFail($leaveTypeId);
        $currentYear = now()->year;

        // Find or create leave balance
        $leaveBalance = LeaveBalance::firstOrCreate(
            [
                'user_id' => $userId,
                'leave_type_id' => $leaveTypeId,
                'year' => $currentYear,
            ],
            [
                'total_earned' => 0,
                'total_used' => 0,
                'current_balance' => 0,
                'carry_over' => 0,
            ]
        );

        // Update leave balance
        $leaveBalance->total_earned += $request->credits_to_add;
        $leaveBalance->current_balance += $request->credits_to_add;
        $leaveBalance->save();
    }
}
