<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Department;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Check if user has Admin role
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        
        $employees = User::with('department')
            ->where('is_active', true)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(15);

        return view('employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Check if user has Admin role
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        
        $departments = Department::where('is_active', true)->get();
        return view('employees.create', compact('departments'));
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Check if user has Admin role
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string|max:255',
            'employment_type' => 'required|in:regular,contractual,part-time',
            'hire_date' => 'required|date',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $employee = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department_id' => $request->department_id,
            'position' => $request->position,
            'employment_type' => $request->employment_type,
            'hire_date' => $request->hire_date,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Assign default employee role
        $employee->assignRole('employee');

        return redirect()->route('employees.index')
            ->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified employee.
     */
    public function show(User $employee)
    {
        $user = Auth::user();
        
        // Check if user has Admin role
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(User $employee)
    {
        $user = Auth::user();
        
        // Check if user has Admin role
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        
        $departments = Department::where('is_active', true)->get();
        return view('employees.edit', compact('employee', 'departments'));
    }

    /**
     * Update the specified employee.
     */
    public function update(Request $request, User $employee)
    {
        $user = Auth::user();
        
        // Check if user has Admin role
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->id,
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string|max:255',
            'employment_type' => 'required|in:regular,contractual,part-time',
            'hire_date' => 'required|date',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $employee->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'email' => $request->email,
            'department_id' => $request->department_id,
            'position' => $request->position,
            'employment_type' => $request->employment_type,
            'hire_date' => $request->hire_date,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
            'is_active' => $request->boolean('is_active', $employee->is_active),
        ]);

        // Update password if provided
        if ($request->filled('password')) {
            $employee->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified employee.
     */
    public function destroy(User $employee)
    {
        $user = Auth::user();
        
        // Check if user has Admin role
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        
        $employee->delete();
        return redirect()->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }
}
