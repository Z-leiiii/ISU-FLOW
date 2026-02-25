<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\AuditLog;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Log the login
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'login',
                'module' => 'authentication',
                'description' => 'User logged in',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        // Log the logout
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'logout',
            'module' => 'authentication',
            'description' => 'User logged out',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Show dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $stats = [
            'leave_balance' => $user->leaveCredits()->sum('credits_balance'),
            'pending_applications' => $user->leaveApplications()->where('status', 'pending')->count(),
            'attendance_this_month' => $user->attendanceRecords()
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->count(),
            'tardiness_this_month' => $user->tardinessRecords()
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->count(),
        ];

        return view('dashboard', compact('user', 'stats'));
    }
}
