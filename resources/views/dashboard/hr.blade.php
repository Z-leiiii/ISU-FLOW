@extends('layouts.app')

@section('title', 'HR Dashboard')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value">{{ $totalApplications }}</div>
        <div class="stat-label">Total Applications</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $pendingApplications }}</div>
        <div class="stat-label">Pending Applications</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $approvedApplications }}</div>
        <div class="stat-label">Approved Applications</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $pendingDesignations }}</div>
        <div class="stat-label">Pending Designations</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Recent Leave Applications</h3>
        <a href="{{ route('hr.leave-applications.index') }}" class="btn btn-primary">
            <i class="fas fa-list"></i> View All
        </a>
    </div>
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Application No.</th>
                    <th>Leave Type</th>
                    <th>Period</th>
                    <th>Days</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentApplications as $application)
                    <tr>
                        <td>{{ $application->user->full_name }}</td>
                        <td>{{ $application->application_number }}</td>
                        <td>
                            {{ $application->leaveType->name }}
                            @if($application->is_without_pay)
                                <span class="badge badge-warning">LWOP</span>
                            @endif
                        </td>
                        <td>{{ $application->start_date->format('M d, Y') }} - {{ $application->end_date->format('M d, Y') }}</td>
                        <td>{{ $application->number_of_days }}</td>
                        <td>
                            <span class="badge badge-{{ $application->status }}">
                                {{ ucfirst($application->status) }}
                            </span>
                        </td>
                        <td>
                            <form action="{{ route('admin.leave-applications.approve', $application) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1rem; margin-right: 0.5rem;">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                            </form>
                            <form action="{{ route('admin.leave-applications.reject', $application) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-secondary" style="padding: 0.5rem 1rem;" onclick="return confirm('Are you sure you want to reject this application?')">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem;">
                            <div style="color: #666;">
                                <i class="fas fa-file-alt" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
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
        <h3 class="card-title">Leave Without Pay Alerts</h3>
    </div>
    <div>
        @forelse($leaveWithoutPayApplications as $application)
            <div style="padding: 1rem; margin-bottom: 0.5rem; background: rgba(245, 158, 11, 0.1); border-radius: 0.5rem; border: 1px solid rgba(245, 158, 11, 0.2);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong>{{ $application->user->full_name }}</strong> - 
                        {{ $application->leaveType->name }} ({{ $application->number_of_days }} days)
                    </div>
                    <span class="badge badge-warning">LWOP</span>
                </div>
                <div style="color: #666; font-size: 0.9rem; margin-top: 0.5rem;">
                    {{ $application->start_date->format('M d, Y') }} - {{ $application->end_date->format('M d, Y') }}
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 2rem; color: #666;">
                <i class="fas fa-check-circle" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                No leave without pay applications found.
            </div>
        @endforelse
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Quick Actions</h3>
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <a href="{{ route('hr.designation-documents.index') }}" class="btn btn-primary">
            <i class="fas fa-file-contract"></i> Review Designations
            @if($pendingDesignations > 0)
                <span class="badge badge-danger" style="margin-left: 0.5rem;">{{ $pendingDesignations }}</span>
            @endif
        </a>
        <a href="{{ route('hr.leave-applications.index') }}" class="btn btn-secondary">
            <i class="fas fa-file-alt"></i> Manage Applications
        </a>
        <a href="{{ route('leave-credits.index') }}" class="btn btn-secondary">
            <i class="fas fa-coins"></i> Update Credits
        </a>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">
            <i class="fas fa-chart-bar"></i> Generate Reports
        </a>
    </div>
</div>
@endsection
