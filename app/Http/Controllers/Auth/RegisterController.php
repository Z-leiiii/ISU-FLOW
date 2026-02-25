<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Department;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        $departments = Department::where('is_active', true)->get();
        return view('auth.register', compact('departments'));
    }

    /**
     * Handle a registration request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'employee_id' => 'required|string|max:20|unique:users,employee_id',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string|max:255',
            'employment_type' => 'required|in:regular,contractual,part-time',
            'hire_date' => 'required|date',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'employee_id' => $request->employee_id,
            'department_id' => $request->department_id,
            'position' => $request->position,
            'employment_type' => $request->employment_type,
            'hire_date' => $request->hire_date,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
            'is_active' => true,
        ]);

        // Assign default employee role
        $user->assignRole('employee');

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Registration successful! Welcome to ISU-Flow.');
    }
}
