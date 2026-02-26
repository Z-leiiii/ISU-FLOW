@extends('layouts.app')

@section('title', 'Employee Dashboard')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value">{{ $leaveBalances->where('leaveType.is_paid', true)->sum('balance') }}</div>
        <div class="stat-label">Total Paid Leave Days</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $recentApplications->where('status', 'pending')->count() }}</div>
        <div class="stat-label">Pending Applications</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $recentApplications->where('status', 'approved')->count() }}</div>
        <div class="stat-label">Approved Applications</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $unreadNotifications->count() }}</div>
        <div class="stat-label">Unread Notifications</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Leave Balances</h3>
        <a href="{{ route('dashboard.leave-balances') }}" class="btn btn-primary">
            <i class="fas fa-eye"></i> View All
        </a>
    </div>
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Leave Type</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leaveBalances as $balance)
                    <tr>
                        <td>{{ $balance->leaveType->name }}</td>
                        <td>
                            <span class="stat-value" style="font-size: 1.2rem;">
                                {{ number_format($balance->balance, 1) }} days
                            </span>
                        </td>
                        <td>
                            @if($balance->balance <= 2)
                                <span class="badge badge-warning">Low Balance</span>
                            @elseif($balance->balance <= 0)
                                <span class="badge badge-danger">No Balance</span>
                            @else
                                <span class="badge badge-success">Good</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('leave-applications.create') }}?leave_type={{ $balance->leave_type_id }}" class="btn btn-primary" style="padding: 0.5rem 1rem;">
                                <i class="fas fa-plus"></i> Apply
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 2rem;">
                            <div style="color: #666;">
                                <i class="fas fa-info-circle" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                                No leave balances found. Please upload your designation document.
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
        <h3 class="card-title">Recent Applications</h3>
        <a href="{{ route('dashboard.leave-history') }}" class="btn btn-secondary">
            <i class="fas fa-history"></i> View History
        </a>
    </div>
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
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
                            <a href="{{ route('leave-applications.show', $application) }}" class="btn btn-secondary" style="padding: 0.5rem 1rem;">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem;">
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

@if(count($leaveWithoutPayWarnings) > 0)
<div class="card" style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2);">
    <div class="card-header">
        <h3 class="card-title" style="color: #d97706;">
            <i class="fas fa-exclamation-triangle"></i> Leave Without Pay Warnings
        </h3>
    </div>
    <div>
        @foreach($leaveWithoutPayWarnings as $warning)
            <div style="padding: 1rem; margin-bottom: 0.5rem; background: rgba(255, 255, 255, 0.8); border-radius: 0.5rem;">
                <i class="fas fa-exclamation-circle" style="color: #d97706; margin-right: 0.5rem;"></i>
                {{ $warning }}
            </div>
        @endforeach
    </div>
</div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Quick Actions</h3>
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <a href="{{ route('leave-applications.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Apply for Leave
        </a>
        <a href="{{ route('dashboard.leave-balances') }}" class="btn btn-secondary">
            <i class="fas fa-balance-scale"></i> Check Balances
        </a>
        <a href="{{ route('designation-documents.create') }}" class="btn btn-secondary">
            <i class="fas fa-upload"></i> Upload Designation
        </a>
        <a href="{{ route('dashboard.leave-history') }}" class="btn btn-secondary">
            <i class="fas fa-history"></i> View History
        </a>
    </div>
</div>
@endsection
