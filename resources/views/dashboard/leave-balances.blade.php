@extends('layouts.app')

@section('title', 'Leave Balances')

@section('content')
@php
    $computationDetails = \App\Services\LeaveCreditService::getLeaveComputationDetails(Auth::user()->id);
@endphp

<div class="page-container">

    {{-- Computation Details --}}
    <div class="computation-card">
        <div class="card-header">
            <h4 class="card-title">Leave Computation Details</h4>
        </div>
        <div class="computation-grid">
            <div class="computation-item">
                <div class="computation-label">Designation Status</div>
                <div class="computation-value">
                    {{ $computationDetails['has_designation'] ? 'With Designation' : 'Without Designation' }}
                </div>
                @if($computationDetails['has_designation'])
                    <div class="computation-subtext">{{ $computationDetails['designation_name'] }}</div>
                @endif
            </div>

            <div class="computation-item">
                <div class="computation-label">Computation Type</div>
                <div class="computation-value">
                    {{ ucfirst(str_replace('_', ' ', $computationDetails['computation_type'])) }}
                </div>
                @if($computationDetails['monthly_rate'])
                    <div class="computation-subtext">{{ $computationDetails['monthly_rate'] }} days/month</div>
                @endif
                @if($computationDetails['annual_rate'])
                    <div class="computation-subtext">{{ $computationDetails['annual_rate'] }} days/year</div>
                @endif
            </div>

            <div class="computation-item">
                <div class="computation-label">Service Period</div>
                <div class="computation-value">{{ $computationDetails['months_worked'] }} months worked</div>
                <div class="computation-subtext">{{ $computationDetails['years_worked'] }} years worked</div>
            </div>

            @if($computationDetails['conversion_formula'])
                <div class="computation-item">
                    <div class="computation-label">Conversion Formula</div>
                    <div class="computation-value">{{ $computationDetails['conversion_formula'] }}</div>
                    <div class="computation-subtext">Service Credits × 69 ÷ 30</div>
                </div>
            @endif
        </div>
    </div>

    {{-- Leave Balances --}}
    @if($leaveBalances->count())
        <div class="computation-card">
            <div class="card-header">
                <h4 class="card-title">Leave Balances</h4>
                <a href="{{ route('dashboard') }}" class="back-link">
                    <i class="fas fa-arrow-left icon-margin"></i>Back to Dashboard
                </a>
            </div>
            <div class="table-container">
                <table class="leave-table">
                    <thead>
                        <tr>
                            <th>Leave Type</th>
                            <th>Total Earned</th>
                            <th>Balance</th>
                            <th>Computation Type</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaveBalances as $balance)
                            @php
                                $computation = \App\Models\LeaveCredit::computeLeaveCredits(Auth::user(), $balance->leaveType);
                            @endphp
                            <tr>
                                <td>
                                    <div class="leave-type-name">{{ $balance->leaveType->name }}</div>
                                    <span class="badge-{{ $balance->leaveType->is_paid ? 'paid' : 'unpaid' }}">
                                        {{ $balance->leaveType->is_paid ? 'Paid' : 'Unpaid' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="balance-amount">{{ number_format($computation['total_earned'], 2) }}</div>
                                    <div class="days-text">days</div>
                                </td>
                                <td>
                                    <div class="balance-amount {{ $computation['balance'] > 0 ? 'balance-positive' : 'balance-negative' }}">
                                        {{ number_format($computation['balance'], 2) }}
                                    </div>
                                    <div class="days-text">days</div>
                                </td>
                                <td>
                                    <span class="computation-badge">
                                        {{ ucfirst(str_replace('_', ' ', $computation['computation_type'])) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-{{ $computation['balance'] > 0 ? 'paid status-good' : 'unpaid status-critical' }}">
                                        {{ $computation['balance'] > 0 ? 'Available' : 'Exhausted' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="days-text">{{ $balance->as_of_date?->format('M d, Y') ?? 'N/A' }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        {{-- Empty State --}}
        <div class="empty-state">
            <div class="empty-state-content">
                <i class="fas fa-balance-scale empty-icon"></i>
                <h4 class="empty-title">No Leave Balances Found</h4>
                <p class="empty-description">Your leave balances haven't been computed yet. This could be because:</p>
                <ul class="empty-list">
                    <li>Your designation document hasn't been approved yet</li>
                    <li>Your leave credits haven't been initialized by HR</li>
                    <li>You're a new employee and the system hasn't been set up for you yet</li>
                </ul>
                <div class="mt-8">
                    <a href="{{ route('designation-documents.create') }}" class="btn">
                        <i class="fas fa-upload"></i> Upload Designation Document
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Leave Computation Rules --}}
    <div class="computation-card">
        <div class="card-header">
            <h4 class="card-title">Leave Computation Rules</h4>
        </div>
        <div class="rules-container" style="color: #333; line-height: 1.6;">
            <div class="rule-section mb-6">
                <h5 class="rule-title text-green-700 flex items-center gap-2 mb-3">
                    <i class="fas fa-user-tie"></i> Faculty With Designation
                </h5>
                <ul class="rules-list">
                    <li>Earn 1.25 days per month (15 vacation leave annually)</li>
                    <li>Earn 1.25 days per month (15 sick leave annually)</li>
                    <li>Total: 30 days of leave credits annually</li>
                </ul>
            </div>
            <div class="rule-section">
                <h5 class="rule-title text-yellow-500 flex items-center gap-2 mb-3">
                    <i class="fas fa-user"></i> Faculty Without Designation
                </h5>
                <ul class="rules-list">
                    <li>Earn 15 days of service credit per year</li>
                    <li>Subject to documentation requirements</li>
                    <li>Conversion formula: Service Credits × 69 ÷ 30</li>
                </ul>
            </div>
        </div>
    </div>

</div>
@endsection
