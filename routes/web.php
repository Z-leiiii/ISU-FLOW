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

// Dashboard route - temporarily outside auth for testing
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Protected routes (require authentication)
Route::middleware(['auth'])->group(function () {
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
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
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
        Route::middleware(['role:hr|admin'])->group(function () {
=======
        Route::middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
        Route::middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
        Route::middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
        Route::middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
        Route::middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
        Route::middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
        Route::middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
        Route::middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
        Route::middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
        Route::middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
        Route::middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
        Route::middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
        Route::middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
        Route::middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
        Route::middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
        Route::middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
        Route::middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
            Route::post('/{designationDocument}/approve', [DesignationDocumentController::class, 'approve'])->name('designation-documents.approve');
            Route::post('/{designationDocument}/reject', [DesignationDocumentController::class, 'reject'])->name('designation-documents.reject');
        });
    });
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
=======
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
    
    // HR Routes (prefix with hr for clarity)
    Route::prefix('hr')->middleware(['role:hr|admin'])->group(function () {
=======
    
    // HR Routes (prefix with hr for clarity)
    Route::prefix('hr')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    
    // HR Routes (prefix with hr for clarity)
    Route::prefix('hr')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    
    // HR Routes (prefix with hr for clarity)
    Route::prefix('hr')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    
    // HR Routes (prefix with hr for clarity)
    Route::prefix('hr')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
        Route::get('/designation-documents', [DesignationDocumentController::class, 'index'])->name('hr.designation-documents.index');
        Route::get('/leave-applications', [LeaveApplicationController::class, 'index'])->name('hr.leave-applications.index');
        Route::get('/employees', [EmployeeController::class, 'index'])->name('hr.employees.index');
    });
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
    
    // HR Routes (prefix with hr for clarity)
    Route::prefix('hr')->middleware(['auth'])->group(function () {
        Route::get('/designation-documents', [DesignationDocumentController::class, 'index'])->name('hr.designation-documents.index');
        Route::get('/leave-applications', [LeaveApplicationController::class, 'adminIndex'])->name('hr.leave-applications.index');
        Route::get('/employees', [EmployeeController::class, 'index'])->name('hr.employees.index');
    });
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
    
    // Leave Applications
    Route::get('/leave-applications', [LeaveApplicationController::class, 'index'])->name('leave-applications.index');
    Route::get('/leave-applications/create', [LeaveApplicationController::class, 'create'])->name('leave-applications.create');
    Route::post('/leave-applications', [LeaveApplicationController::class, 'store'])->name('leave-applications.store');
    Route::get('/leave-applications/{leaveApplication}', [LeaveApplicationController::class, 'show'])->name('leave-applications.show');
    Route::get('/leave-applications/{leaveApplication}/edit', [LeaveApplicationController::class, 'edit'])->name('leave-applications.edit');
    Route::put('/leave-applications/{leaveApplication}', [LeaveApplicationController::class, 'update'])->name('leave-applications.update');
    Route::delete('/leave-applications/{leaveApplication}', [LeaveApplicationController::class, 'destroy'])->name('leave-applications.destroy');
    
    // Leave Credits (HR/Admin only)
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
    Route::prefix('leave-credits')->middleware(['role:hr|admin'])->group(function () {
=======
    Route::prefix('leave-credits')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('leave-credits')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('leave-credits')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('leave-credits')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('leave-credits')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('leave-credits')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('leave-credits')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('leave-credits')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('leave-credits')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('leave-credits')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('leave-credits')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('leave-credits')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('leave-credits')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('leave-credits')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('leave-credits')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('leave-credits')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('leave-credits')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
        Route::get('/', [LeaveCreditController::class, 'index'])->name('leave-credits.index');
        Route::get('/create', [LeaveCreditController::class, 'create'])->name('leave-credits.create');
        Route::post('/', [LeaveCreditController::class, 'store'])->name('leave-credits.store');
        Route::post('/update-balance/{userId}/{leaveTypeId}', [LeaveCreditController::class, 'updateLeaveBalance'])->name('leave-credits.update-balance');
    });
    
    // Reports (HR/Admin only)
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/leave-summary', [ReportController::class, 'leaveSummary'])->name('reports.leave-summary');
    Route::get('/reports/attendance-report', [ReportController::class, 'attendanceReport'])->name('reports.attendance-report');
    Route::get('/reports/leave-credits', [ReportController::class, 'leaveCreditsReport'])->name('reports.leave-credits');
=======
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
    Route::prefix('reports')->middleware(['role:hr|admin'])->group(function () {
=======
    Route::prefix('reports')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('reports')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('reports')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('reports')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('reports')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('reports')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('reports')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('reports')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('reports')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('reports')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('reports')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('reports')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('reports')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('reports')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('reports')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('reports')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('reports')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
        Route::get('/', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/leave-summary', [ReportController::class, 'leaveSummary'])->name('reports.leave-summary');
        Route::get('/reports/attendance-report', [ReportController::class, 'attendanceReport'])->name('reports.attendance-report');
        Route::get('/reports/leave-credits', [ReportController::class, 'leaveCreditsReport'])->name('reports.leave-credits');
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
=======
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
    });
    
    // Attendance routes
    Route::prefix('attendance')->middleware(['auth'])->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('attendance.index');
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
    });
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
    
    // Admin Routes (Admin only)
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
    Route::prefix('employees')->middleware(['role:admin'])->group(function () {
=======
    Route::prefix('employees')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('employees')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('employees')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('employees')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('employees')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('employees')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('employees')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('employees')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('employees')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('employees')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('employees')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('employees')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('employees')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('employees')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('employees')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('employees')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('employees')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
        Route::get('/', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    });
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
=======
    
    // Admin Routes (for HR and administrators)
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
<<<<<<< C:/ISU-FLOW/systemF/routes/web.php
    Route::prefix('admin')->middleware(['role:hr|admin'])->group(function () {
=======
    Route::prefix('admin')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('admin')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('admin')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('admin')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('admin')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('admin')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('admin')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('admin')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('admin')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('admin')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('admin')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('admin')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('admin')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('admin')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('admin')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('admin')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
=======
    Route::prefix('admin')->middleware(['auth'])->group(function () {
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
        Route::get('/leave-applications', [LeaveApplicationController::class, 'adminIndex'])->name('admin.leave-applications.index');
        Route::post('/leave-applications/{leaveApplication}/approve', [LeaveApplicationController::class, 'approve'])->name('admin.leave-applications.approve');
        Route::post('/leave-applications/{leaveApplication}/reject', [LeaveApplicationController::class, 'reject'])->name('admin.leave-applications.reject');
    });
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/routes/web.php
});

// Redirect root to dashboard for authenticated users, login for guests
Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
});
