@extends('layouts.app')

@section('title', 'Leave Applications Management')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Leave Applications Management</h3>
        <div class="inline-flex items-center gap-2">
            <span class="badge-success">
                {{ $applications->count() }} Total Applications
            </span>
            <span class="badge-warning">
                {{ $applications->where('status', 'pending')->count() }} Pending
            </span>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="flex gap-4 mb-6 flex-wrap">
        <form method="GET" class="flex items-center gap-2">
            <select name="status" class="form-select" style="width: auto;">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <select name="leave_type" class="form-select" style="width: auto;">
                <option value="">All Leave Types</option>
                @foreach($leaveTypes as $type)
                    <option value="{{ $type->id }}" {{ request('leave_type') == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('hr.leave-applications.index') }}" class="btn btn-secondary">Clear</a>
        </form>
    </div>
    
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Leave Type</th>
                    <th>Duration</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Applied On</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $application)
                    <tr>
                        <td>
                            <div class="font-weight-600">{{ $application->user->full_name }}</div>
                            <div class="text-sm text-gray-600">{{ $application->user->email }}</div>
                            @if($application->user->department)
                                <div class="text-sm text-gray-600">{{ $application->user->department->name }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="font-weight-600">{{ $application->leaveType->name }}</div>
                            <div class="text-sm text-gray-600">{{ $application->days_requested }} days</div>
                        </td>
                        <td>
                            <div class="font-weight-600">{{ $application->start_date->format('M d, Y') }}</div>
                            <div class="text-sm text-gray-600">to {{ $application->end_date->format('M d, Y') }}</div>
                        </td>
                        <td>
                            <div class="text-truncate" title="{{ $application->reason }}">
                                {{ $application->reason }}
                            </div>
                        </td>
                        <td>
                            @if($application->status == 'pending')
                                <span class="badge badge-warning">
                                    <i class="fas fa-clock"></i> Pending
                                </span>
                            @elseif($application->status == 'approved')
                                <span class="badge badge-success">
                                    <i class="fas fa-check"></i> Approved
                                </span>
                            @elseif($application->status == 'rejected')
                                <span class="badge badge-danger">
                                    <i class="fas fa-times"></i> Rejected
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="text-sm text-gray-600">
                                {{ $application->created_at->format('M d, Y') }}
                            </div>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('leave-applications.show', $application) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                @if($application->status == 'pending')
                                    <form method="POST" action="{{ route('admin.leave-applications.approve', $application) }}" class="inline-form">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to approve this leave application?')">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.leave-applications.reject', $application) }}" class="inline-form">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to reject this leave application?')">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center p-8">
                            <div class="text-gray-600">
                                <i class="fas fa-file-alt empty-icon"></i>
                                No leave applications found.
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
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="{{ route('leave-credits.index') }}" class="btn btn-secondary">
            <i class="fas fa-coins"></i> Leave Credits
        </a>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">
            <i class="fas fa-chart-bar"></i> Reports
        </a>
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
            <i class="fas fa-info-circle"></i> Applications Summary
        </h3>
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; color: #333;">
        <div style="text-align: center; padding: 1rem; background: rgba(255, 255, 255, 0.8); border-radius: 0.5rem;">
            <div style="font-size: 1.5rem; font-weight: 700; color: #667eea;">{{ $applications->count() }}</div>
            <div style="font-size: 0.85rem;">Total Applications</div>
        </div>
        <div style="text-align: center; padding: 1rem; background: rgba(255, 255, 255, 0.8); border-radius: 0.5rem;">
            <div style="font-size: 1.5rem; font-weight: 700; color: #f59e0b;">{{ $applications->where('status', 'pending')->count() }}</div>
            <div style="font-size: 0.85rem;">Pending</div>
        </div>
        <div style="text-align: center; padding: 1rem; background: rgba(255, 255, 255, 0.8); border-radius: 0.5rem;">
            <div style="font-size: 1.5rem; font-weight: 700; color: #10b981;">{{ $applications->where('status', 'approved')->count() }}</div>
            <div style="font-size: 0.85rem;">Approved</div>
        </div>
        <div style="text-align: center; padding: 1rem; background: rgba(255, 255, 255, 0.8); border-radius: 0.5rem;">
            <div style="font-size: 1.5rem; font-weight: 700; color: #ef4444;">{{ $applications->where('status', 'rejected')->count() }}</div>
            <div style="font-size: 0.85rem;">Rejected</div>
        </div>
    </div>
</div>
@endsection
