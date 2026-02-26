@extends('layouts.app')

@section('title', 'Leave Balances')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Leave Balances</h3>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
    
    @if($leaveBalances->count() > 0)
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Leave Type</th>
                        <th>Total Earned</th>
                        <th>Total Used</th>
                        <th>Remaining Balance</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaveBalances as $balance)
                        <tr>
                            <td>
                                <strong>{{ $balance->leaveType->name }}</strong>
                                @if(!$balance->leaveType->is_paid)
                                    <span class="badge badge-info">Unpaid</span>
                                @endif
                            </td>
                            <td>{{ number_format($balance->total_earned, 1) }} days</td>
                            <td>{{ number_format($balance->total_used, 1) }} days</td>
                            <td>
                                <span class="stat-value" style="font-size: 1.2rem; color: {{ $balance->balance <= 1 ? '#dc2626' : ($balance->balance <= 3 ? '#d97706' : '#16a34a') }};">
                                    {{ number_format($balance->balance, 1) }} days
                                </span>
                            </td>
                            <td>
                                @if($balance->balance <= 0)
                                    <span class="badge badge-danger">No Balance</span>
                                @elseif($balance->balance <= 2)
                                    <span class="badge badge-warning">Low Balance</span>
                                @else
                                    <span class="badge badge-success">Good</span>
                                @endif
                            </td>
                            <td>{{ $balance->as_of_date->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 2rem; padding: 1.5rem; background: rgba(59, 130, 246, 0.1); border-radius: 0.5rem; border: 1px solid rgba(59, 130, 246, 0.2);">
            <h4 style="color: #2563eb; margin-bottom: 1rem;">
                <i class="fas fa-info-circle"></i> Leave Balance Information
            </h4>
            <div style="color: #333; line-height: 1.6;">
                <p><strong>Total Paid Leave Days:</strong> {{ $leaveBalances->where('leaveType.is_paid', true)->sum('balance') }} days</p>
                <p><strong>Total Unpaid Leave Days:</strong> {{ $leaveBalances->where('leaveType.is_paid', false)->sum('balance') }} days</p>
                <p><strong>Leave Types with Low Balance:</strong> {{ $leaveBalances->where('balance', '<=', 2)->count() }} types</p>
                <p><strong>Last Updated:</strong> {{ $leaveBalances->max('as_of_date')->format('F d, Y h:i A') }}</p>
            </div>
        </div>
    @else
        <div style="text-align: center; padding: 3rem; color: #666;">
            <i class="fas fa-balance-scale" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
            <h3>No Leave Balances Found</h3>
            <p style="margin-top: 1rem;">You don't have any leave balances yet. This could be because:</p>
            <ul style="text-align: left; max-width: 400px; margin: 1rem auto; color: #666;">
                <li>Your designation document hasn't been approved yet</li>
                <li>Your leave credits haven't been initialized by HR</li>
                <li>You're a new employee and the system hasn't been set up for you yet</li>
            </ul>
            <div style="margin-top: 2rem;">
                <a href="{{ route('designation-documents.create') }}" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Upload Designation Document
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary" style="margin-left: 1rem;">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    @endif
</div>

@if($leaveBalances->count() > 0)
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Quick Actions</h3>
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <a href="{{ route('leave-applications.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Apply for Leave
        </a>
        <a href="{{ route('dashboard.leave-history') }}" class="btn btn-secondary">
            <i class="fas fa-history"></i> View Leave History
        </a>
        <a href="{{ route('designation-documents.create') }}" class="btn btn-secondary">
            <i class="fas fa-upload"></i> Upload Designation
        </a>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-tachometer-alt"></i> Back to Dashboard
        </a>
    </div>
</div>
@endif
@endsection
