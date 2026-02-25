<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeaveApplicationController;
use App\Http\Controllers\LeaveCreditController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\EmployeeController;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register');

// Protected routes (require authentication)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
    // Leave Applications
    Route::get('/leave-applications', [LeaveApplicationController::class, 'index'])->name('leave-applications.index');
    Route::get('/leave-applications/create', [LeaveApplicationController::class, 'create'])->name('leave-applications.create');
    Route::post('/leave-applications', [LeaveApplicationController::class, 'store'])->name('leave-applications.store');
    Route::get('/leave-applications/{leaveApplication}', [LeaveApplicationController::class, 'show'])->name('leave-applications.show');
    Route::get('/leave-applications/{leaveApplication}/edit', [LeaveApplicationController::class, 'edit'])->name('leave-applications.edit');
    Route::put('/leave-applications/{leaveApplication}', [LeaveApplicationController::class, 'update'])->name('leave-applications.update');
    Route::delete('/leave-applications/{leaveApplication}', [LeaveApplicationController::class, 'destroy'])->name('leave-applications.destroy');
    
    // Leave Credits (HR/Admin only)
    Route::prefix('leave-credits')->group(function () {
        Route::get('/', [LeaveCreditController::class, 'index'])->name('leave-credits.index');
        Route::get('/create', [LeaveCreditController::class, 'create'])->name('leave-credits.create');
        Route::post('/', [LeaveCreditController::class, 'store'])->name('leave-credits.store');
        Route::post('/update-balance/{userId}/{leaveTypeId}', [LeaveCreditController::class, 'updateLeaveBalance'])->name('leave-credits.update-balance');
    });
    
    // Attendance (HR/Admin only for management, Employee for viewing)
    Route::prefix('attendance')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/current-status', [AttendanceController::class, 'currentStatus'])->name('attendance.current-status');
        
        // Management routes (HR/Admin only)
        Route::group([], function () {
            Route::post('/', [AttendanceController::class, 'store'])->name('attendance.store');
            Route::post('/time-in', [AttendanceController::class, 'timeIn'])->name('attendance.time-in');
            Route::post('/time-out', [AttendanceController::class, 'timeOut'])->name('attendance.time-out');
        });
    });
    
    // Reports (HR/Admin only)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/leave-summary', [ReportController::class, 'leaveSummary'])->name('reports.leave-summary');
    Route::get('/reports/attendance-report', [ReportController::class, 'attendanceReport'])->name('reports.attendance-report');
    Route::get('/reports/leave-credits', [ReportController::class, 'leaveCreditsReport'])->name('reports.leave-credits');
    
    // API Routes
    Route::get('/api/employees', function () {
        $user = Auth::user();
        if (!$user->hasRole('admin') && !$user->hasRole('hr')) {
            abort(403, 'Unauthorized action.');
        }
        
        return User::where('is_active', true)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'full_name']);
    });
    
    // Admin Routes (Admin only)
    Route::prefix('employees')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    });
    
    // Admin Routes (for HR and administrators)
    Route::prefix('admin')->group(function () {
        Route::get('/leave-applications', [LeaveApplicationController::class, 'adminIndex'])->name('admin.leave-applications.index');
        Route::post('/leave-applications/{leaveApplication}/approve', [LeaveApplicationController::class, 'approve'])->name('admin.leave-applications.approve');
        Route::post('/leave-applications/{leaveApplication}/reject', [LeaveApplicationController::class, 'reject'])->name('admin.leave-applications.reject');
    });
});

// Redirect root to dashboard for authenticated users
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});
