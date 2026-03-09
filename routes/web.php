<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaveApplicationController;
use App\Http\Controllers\LeaveCreditController;
use App\Http\Controllers\DesignationDocumentController;
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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/leave-balances', [DashboardController::class, 'leaveBalances'])->name('dashboard.leave-balances');
    Route::get('/leave-history', [DashboardController::class, 'leaveHistory'])->name('dashboard.leave-history');
    
    // Designation Documents
    Route::prefix('designation-documents')->group(function () {
        Route::get('/', [DesignationDocumentController::class, 'index'])->name('designation-documents.index');
        Route::get('/create', [DesignationDocumentController::class, 'create'])->name('designation-documents.create');
        Route::post('/', [DesignationDocumentController::class, 'store'])->name('designation-documents.store');
        Route::get('/{designationDocument}', [DesignationDocumentController::class, 'show'])->name('designation-documents.show');
        Route::get('/{designationDocument}/edit', [DesignationDocumentController::class, 'edit'])->name('designation-documents.edit');
        Route::put('/{designationDocument}', [DesignationDocumentController::class, 'update'])->name('designation-documents.update');
        Route::delete('/{designationDocument}', [DesignationDocumentController::class, 'destroy'])->name('designation-documents.destroy');
        Route::get('/{designationDocument}/download', [DesignationDocumentController::class, 'download'])->name('designation-documents.download');
        
        // HR/Admin routes
        Route::middleware(['auth'])->group(function () {
            Route::post('/{designationDocument}/approve', [DesignationDocumentController::class, 'approve'])->name('designation-documents.approve');
            Route::post('/{designationDocument}/reject', [DesignationDocumentController::class, 'reject'])->name('designation-documents.reject');
        });
    });
    
    // HR Routes (prefix with hr for clarity)
    Route::prefix('hr')->middleware(['auth'])->group(function () {
        Route::get('/designation-documents', [DesignationDocumentController::class, 'index'])->name('hr.designation-documents.index');
        Route::get('/leave-applications', [LeaveApplicationController::class, 'adminIndex'])->name('hr.leave-applications.index');
        Route::get('/employees', [EmployeeController::class, 'index'])->name('hr.employees.index');
    });
    
    // Leave Applications
    Route::get('/leave-applications', [LeaveApplicationController::class, 'index'])->name('leave-applications.index');
    Route::get('/leave-applications/create', [LeaveApplicationController::class, 'create'])->name('leave-applications.create');
    Route::post('/leave-applications', [LeaveApplicationController::class, 'store'])->name('leave-applications.store');
    Route::get('/leave-applications/{leaveApplication}', [LeaveApplicationController::class, 'show'])->name('leave-applications.show');
    Route::get('/leave-applications/{leaveApplication}/edit', [LeaveApplicationController::class, 'edit'])->name('leave-applications.edit');
    Route::put('/leave-applications/{leaveApplication}', [LeaveApplicationController::class, 'update'])->name('leave-applications.update');
    Route::delete('/leave-applications/{leaveApplication}', [LeaveApplicationController::class, 'destroy'])->name('leave-applications.destroy');
    
    // Leave Credits (HR/Admin only)
    Route::prefix('leave-credits')->middleware(['auth'])->group(function () {
        Route::get('/', [LeaveCreditController::class, 'index'])->name('leave-credits.index');
        Route::get('/create', [LeaveCreditController::class, 'create'])->name('leave-credits.create');
        Route::post('/', [LeaveCreditController::class, 'store'])->name('leave-credits.store');
        Route::post('/update-balance/{userId}/{leaveTypeId}', [LeaveCreditController::class, 'updateLeaveBalance'])->name('leave-credits.update-balance');
    });
    
    // Reports (HR/Admin only)
    Route::prefix('reports')->middleware(['auth'])->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/leave-summary', [ReportController::class, 'leaveSummary'])->name('reports.leave-summary');
        Route::get('/reports/attendance-report', [ReportController::class, 'attendanceReport'])->name('reports.attendance-report');
        Route::get('/reports/leave-credits', [ReportController::class, 'leaveCreditsReport'])->name('reports.leave-credits');
    });
    
    // Attendance routes
    Route::prefix('attendance')->middleware(['auth'])->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('attendance.index');
    });
    
    // Admin Routes (Admin only)
    Route::prefix('employees')->middleware(['auth'])->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    });
    
    // Admin Routes (for HR and administrators)
    Route::prefix('admin')->middleware(['auth'])->group(function () {
        Route::get('/leave-applications', [LeaveApplicationController::class, 'adminIndex'])->name('admin.leave-applications.index');
        Route::post('/leave-applications/{leaveApplication}/approve', [LeaveApplicationController::class, 'approve'])->name('admin.leave-applications.approve');
        Route::post('/leave-applications/{leaveApplication}/reject', [LeaveApplicationController::class, 'reject'])->name('admin.leave-applications.reject');
    });
});

// Redirect root to dashboard for authenticated users
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});
