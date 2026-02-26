@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Reports</h3>
        <div style="color: #666; font-size: 0.9rem;">
            Generate and view various system reports
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
        <!-- Leave Summary Report -->
        <div class="card" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); border: 1px solid rgba(102, 126, 234, 0.2);">
            <div class="card-header">
                <h4 style="color: #667eea; font-size: 1.1rem;">
                    <i class="fas fa-file-alt"></i> Leave Summary Report
                </h4>
            </div>
            <div style="color: #333; line-height: 1.6; margin-bottom: 1rem;">
                View comprehensive leave statistics including approved, pending, and rejected applications across all leave types.
            </div>
            <a href="{{ route('reports.leave-summary') }}" class="btn btn-primary" style="width: 100%;">
                <i class="fas fa-chart-line"></i> Generate Report
            </a>
        </div>
        
        <!-- Attendance Report -->
        <div class="card" style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(16, 185, 129, 0.1) 100%); border: 1px solid rgba(34, 197, 94, 0.2);">
            <div class="card-header">
                <h4 style="color: #16a34a; font-size: 1.1rem;">
                    <i class="fas fa-calendar-check"></i> Attendance Report
                </h4>
            </div>
            <div style="color: #333; line-height: 1.6; margin-bottom: 1rem;">
                Generate attendance reports showing employee presence, absences, and leave patterns for specific periods.
            </div>
            <a href="{{ route('reports.attendance-report') }}" class="btn btn-primary" style="width: 100%; background: linear-gradient(135deg, #16a34a 0%, #10b981 100%);">
                <i class="fas fa-chart-bar"></i> Generate Report
            </a>
        </div>
        
        <!-- Leave Credits Report -->
        <div class="card" style="background: linear-gradient(135deg, rgba(251, 146, 60, 0.1) 0%, rgba(251, 113, 133, 0.1) 100%); border: 1px solid rgba(251, 146, 60, 0.2);">
            <div class="card-header">
                <h4 style="color: #ea580c; font-size: 1.1rem;">
                    <i class="fas fa-coins"></i> Leave Credits Report
                </h4>
            </div>
            <div style="color: #333; line-height: 1.6; margin-bottom: 1rem;">
                View detailed leave credit balances for all employees across different leave types and departments.
            </div>
            <a href="{{ route('reports.leave-credits') }}" class="btn btn-primary" style="width: 100%; background: linear-gradient(135deg, #ea580c 0%, #fb7185 100%);">
                <i class="fas fa-wallet"></i> Generate Report
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Quick Actions</h3>
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="{{ route('leave-applications.index') }}" class="btn btn-secondary">
            <i class="fas fa-file-alt"></i> Leave Applications
        </a>
        @if(Auth::user()->hasAnyRole(['admin', 'hr']))
        <a href="{{ route('leave-credits.index') }}" class="btn btn-secondary">
            <i class="fas fa-coins"></i> Leave Credits
        </a>
        @endif
        @if(Auth::user()->hasRole('admin'))
        <a href="{{ route('employees.index') }}" class="btn btn-secondary">
            <i class="fas fa-users"></i> Employees
        </a>
        @endif
    </div>
</div>

<div class="card" style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2);">
    <div class="card-header">
        <h3 class="card-title" style="color: #2563eb;">
            <i class="fas fa-info-circle"></i> Report Information
        </h3>
    </div>
    <div style="color: #333; line-height: 1.6;">
        <p><strong>Available Reports:</strong></p>
        <ul style="margin: 0; padding-left: 1.5rem;">
            <li><strong>Leave Summary:</strong> Comprehensive overview of all leave applications</li>
            <li><strong>Attendance Report:</strong> Employee attendance and presence patterns</li>
            <li><strong>Leave Credits:</strong> Current leave balances across all employees</li>
        </ul>
        <p style="margin-top: 1rem;"><strong>Export Options:</strong> All reports can be exported to PDF or Excel format for further analysis.</p>
    </div>
</div>
@endsection
