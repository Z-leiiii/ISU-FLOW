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
use App\Http\Controllers\DTRController;
use App\Http\Controllers\HR\DashboardController as HRDashboardController;
use App\Http\Controllers\Faculty\DashboardController as FacultyDashboardController;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

/*
|--------------------------------------------------------------------------
| Protected Routes (Authenticated Users)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/leave-balances', [DashboardController::class, 'leaveBalances'])->name('dashboard.leave-balances');
    Route::get('/leave-history', [DashboardController::class, 'leaveHistory'])->name('dashboard.leave-history');

    // Leave Applications (User-level)
    Route::resource('leave-applications', LeaveApplicationController::class);

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

        // HR/Admin approval actions
        Route::middleware(['role:hr|admin'])->group(function () {
            Route::post('/{designationDocument}/approve', [DesignationDocumentController::class, 'approve'])->name('designation-documents.approve');
            Route::post('/{designationDocument}/reject', [DesignationDocumentController::class, 'reject'])->name('designation-documents.reject');
        });
    });

    // Leave Credits (HR/Admin only)
    Route::prefix('leave-credits')->middleware(['role:hr|admin'])->group(function () {
        Route::get('/', [LeaveCreditController::class, 'index'])->name('leave-credits.index');
        Route::get('/create', [LeaveCreditController::class, 'create'])->name('leave-credits.create');
        Route::post('/', [LeaveCreditController::class, 'store'])->name('leave-credits.store');
        Route::post('/update-balance/{userId}/{leaveTypeId}', [LeaveCreditController::class, 'updateLeaveBalance'])->name('leave-credits.update-balance');
    });

    // Reports (HR/Admin only)
    Route::prefix('reports')->middleware(['role:hr|admin'])->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/leave-summary', [ReportController::class, 'leaveSummary'])->name('reports.leave-summary');
        Route::get('/attendance-report', [ReportController::class, 'attendanceReport'])->name('reports.attendance-report');
        Route::get('/leave-credits', [ReportController::class, 'leaveCreditsReport'])->name('reports.leave-credits');
    });

    // Attendance
    Route::prefix('attendance')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('attendance.index');
    });

    // Employees (Admin only)
    Route::prefix('employees')->middleware(['role:admin'])->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    });

    // Admin Leave Approval (HR/Admin)
    Route::prefix('admin')->middleware(['role:hr|admin'])->group(function () {
        Route::get('/leave-applications', [LeaveApplicationController::class, 'adminIndex'])->name('admin.leave-applications.index');
        Route::post('/leave-applications/{leaveApplication}/approve', [LeaveApplicationController::class, 'approve'])->name('admin.leave-applications.approve');
        Route::post('/leave-applications/{leaveApplication}/reject', [LeaveApplicationController::class, 'reject'])->name('admin.leave-applications.reject');
    });

    // DTR Routes
    Route::prefix('dtr')->group(function () {
        Route::get('/', [DTRController::class, 'index'])->name('dtr.index');
        Route::get('/create', [DTRController::class, 'create'])->name('dtr.create');
        Route::post('/', [DTRController::class, 'store'])->name('dtr.store');
        Route::get('/{dtr}', [DTRController::class, 'show'])->name('dtr.show');
        Route::get('/{dtr}/edit', [DTRController::class, 'edit'])->name('dtr.edit');
        Route::put('/{dtr}', [DTRController::class, 'update'])->name('dtr.update');
        Route::post('/{dtr}/approve', [DTRController::class, 'approve'])->name('dtr.approve');
        Route::post('/{dtr}/reject', [DTRController::class, 'reject'])->name('dtr.reject');
        Route::post('/bulk-approve', [DTRController::class, 'bulkApprove'])->name('dtr.bulk-approve');
        Route::get('/pending-submissions', [DTRController::class, 'getPendingSubmissions'])->name('dtr.pending-submissions');
        Route::get('/export', [DTRController::class, 'export'])->name('dtr.export');
    });

    // HR Dashboard
    Route::prefix('hr')->middleware(['role:hr|admin'])->group(function () {
        Route::get('/dashboard', [HRDashboardController::class, 'index'])->name('hr.dashboard');
        Route::post('/validate-dtr/{dtr}', [HRDashboardController::class, 'validateDTR'])->name('hr.validate-dtr');
        Route::post('/bulk-validate-dtr', [HRDashboardController::class, 'bulkValidateDTR'])->name('hr.bulk-validate-dtr');
        Route::post('/sync-monthly-balances', [HRDashboardController::class, 'syncMonthlyBalances'])->name('hr.sync-monthly-balances');
    });

    // Faculty Dashboard
    Route::prefix('faculty')->middleware(['role:faculty'])->group(function () {
        Route::get('/dashboard', [FacultyDashboardController::class, 'index'])->name('faculty.dashboard');
        Route::post('/submit-dtr', [FacultyDashboardController::class, 'submitDTR'])->name('faculty.submit-dtr');
        Route::get('/balance-details', [FacultyDashboardController::class, 'getBalanceDetails'])->name('faculty.balance-details');
        Route::post('/mark-notification-read/{notification}', [FacultyDashboardController::class, 'markNotificationRead'])->name('faculty.mark-notification-read');
    });
});

/*
|--------------------------------------------------------------------------
| Root Redirect
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});