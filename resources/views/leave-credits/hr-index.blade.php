@extends('layouts.app')

@section('title', 'Leave Credits Management')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Leave Credits Management</h3>
        <a href="{{ route('leave-credits.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Leave Credit
        </a>
    </div>
    
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Department</th>
                    <th>Vacation Leave</th>
                    <th>Sick Leave</th>
                    <th>Mandatory Leave</th>
                    <th>Other Leave</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                    <tr>
                        <td>
                            <div style="font-weight: 600;">{{ $employee->full_name }}</div>
                            <div style="font-size: 0.85rem; color: #666;">{{ $employee->email }}</div>
                        </td>
                        <td>{{ $employee->department ? $employee->department->name : 'N/A' }}</td>
                        <td>
                            @php
                                $vacationBalance = $employee->leaveCredits->where('leave_type_id', 1)->first();
                            @endphp
                            <span class="stat-value" style="font-size: 1.1rem;">
                                {{ $vacationBalance ? number_format($vacationBalance->balance, 1) : '0.0' }} days
                            </span>
                        </td>
                        <td>
                            @php
                                $sickBalance = $employee->leaveCredits->where('leave_type_id', 2)->first();
                            @endphp
                            <span class="stat-value" style="font-size: 1.1rem;">
                                {{ $sickBalance ? number_format($sickBalance->balance, 1) : '0.0' }} days
                            </span>
                        </td>
                        <td>
                            @php
                                $mandatoryBalance = $employee->leaveCredits->where('leave_type_id', 3)->first();
                            @endphp
                            <span class="stat-value" style="font-size: 1.1rem;">
                                {{ $mandatoryBalance ? number_format($mandatoryBalance->balance, 1) : '0.0' }} days
                            </span>
                        </td>
                        <td>
                            @php
                                $otherCredits = $employee->leaveCredits->whereNotIn('leave_type_id', [1, 2, 3]);
                                $totalOther = $otherCredits->sum('balance');
                            @endphp
                            <span class="stat-value" style="font-size: 1.1rem;">
                                {{ number_format($totalOther, 1) }} days
                            </span>
                        </td>
                        <td>
                            <!-- DEBUG: Form should use POST method -->
                            <form method="POST" action="{{ route('leave-credits.update-balance', ['userId' => $employee->id, 'leaveTypeId' => 1]) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1rem;">
                                    <i class="fas fa-edit"></i> Update (POST)
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem;">
                            <div style="color: #666;">
                                <i class="fas fa-users" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                                No employees found.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Quick Actions</h3>
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <a href="{{ route('leave-credits.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Leave Credit
        </a>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-tachometer-alt"></i> Back to Dashboard
        </a>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">
            <i class="fas fa-chart-bar"></i> Generate Reports
        </a>
        <a href="{{ route('hr.employees.index') }}" class="btn btn-secondary">
            <i class="fas fa-users"></i> Manage Employees
        </a>
    </div>
</div>

<div class="card" style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2);">
    <div class="card-header">
        <h3 class="card-title" style="color: #2563eb;">
            <i class="fas fa-info-circle"></i> Leave Credits Information
        </h3>
    </div>
    <div style="color: #333; line-height: 1.6;">
        <p><strong>Total Employees:</strong> {{ $employees->count() }}</p>
        <p><strong>Total Vacation Leave Days:</strong> {{ $employees->sum(function($emp) { return $emp->leaveCredits->where('leave_type_id', 1)->first()->balance ?? 0; }) }} days</p>
        <p><strong>Total Sick Leave Days:</strong> {{ $employees->sum(function($emp) { return $emp->leaveCredits->where('leave_type_id', 2)->first()->balance ?? 0; }) }} days</p>
        <p><strong>Total Mandatory Leave Days:</strong> {{ $employees->sum(function($emp) { return $emp->leaveCredits->where('leave_type_id', 3)->first()->balance ?? 0; }) }} days</p>
    </div>
</div>
@endsection
