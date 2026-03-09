@extends('layouts.app')

@section('title', 'Leave Balances')

@section('content')
<!-- Computation Details -->
@php
    $computationDetails = \App\Services\LeaveCreditService::getLeaveComputationDetails(Auth::user()->id);
@endphp

<div style="margin-bottom: 2rem;">
    <div style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 1rem; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h4 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: #333;">Leave Computation Details</h4>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div style="background: rgba(4, 120, 87, 0.05); border: 1px solid rgba(4, 120, 87, 0.1); border-radius: 0.75rem; padding: 1rem;">
                <div style="font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem;">Designation Status</div>
                <div style="font-size: 1.125rem; font-weight: 600; color: #333;">{{ $computationDetails['has_designation'] ? 'With Designation' : 'Without Designation' }}</div>
                @if($computationDetails['has_designation'])
                    <div style="font-size: 0.875rem; color: #666; margin-top: 0.25rem;">{{ $computationDetails['designation_name'] }}</div>
                @endif
            </div>
            <div style="background: rgba(4, 120, 87, 0.05); border: 1px solid rgba(4, 120, 87, 0.1); border-radius: 0.75rem; padding: 1rem;">
                <div style="font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem;">Computation Type</div>
                <div style="font-size: 1.125rem; font-weight: 600; color: #333;">{{ ucfirst(str_replace('_', ' ', $computationDetails['computation_type'])) }}</div>
                @if($computationDetails['monthly_rate'])
                    <div style="font-size: 0.875rem; color: #666; margin-top: 0.25rem;">{{ $computationDetails['monthly_rate'] }} days/month</div>
                @endif
                @if($computationDetails['annual_rate'])
                    <div style="font-size: 0.875rem; color: #666; margin-top: 0.25rem;">{{ $computationDetails['annual_rate'] }} days/year</div>
                @endif
            </div>
            <div style="background: rgba(4, 120, 87, 0.05); border: 1px solid rgba(4, 120, 87, 0.1); border-radius: 0.75rem; padding: 1rem;">
                <div style="font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem;">Service Period</div>
                <div style="font-size: 1.125rem; font-weight: 600; color: #333;">{{ $computationDetails['months_worked'] }} months worked</div>
                <div style="font-size: 0.875rem; color: #666; margin-top: 0.25rem;">{{ $computationDetails['years_worked'] }} years worked</div>
            </div>
            @if($computationDetails['conversion_formula'])
                <div style="background: rgba(4, 120, 87, 0.05); border: 1px solid rgba(4, 120, 87, 0.1); border-radius: 0.75rem; padding: 1rem;">
                    <div style="font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem;">Conversion Formula</div>
                    <div style="font-size: 1.125rem; font-weight: 600; color: #333;">{{ $computationDetails['conversion_formula'] }}</div>
                    <div style="font-size: 0.875rem; color: #666; margin-top: 0.25rem;">Service Credits × 69 ÷ 30</div>
                </div>
            @endif
        </div>
    </div>
</div>

@if($leaveBalances->count() > 0)
    <div style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 1rem; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h4 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: #333;">Leave Balances</h4>
            <a href="{{ route('dashboard') }}" style="color: #047857; text-decoration: none; font-weight: 500;">
                <i class="fas fa-arrow-left" style="margin-right: 0.5rem;"></i>Back to Dashboard
            </a>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; background: rgba(255, 255, 255, 0.95); border-radius: 0.5rem; overflow: hidden;">
                <thead>
                    <tr style="background: rgba(4, 120, 87, 0.1);">
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: #333; border-bottom: 1px solid rgba(0, 0, 0, 0.05);">Leave Type</th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: #333; border-bottom: 1px solid rgba(0, 0, 0, 0.05);">Total Earned</th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: #333; border-bottom: 1px solid rgba(0, 0, 0, 0.05);">Balance</th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: #333; border-bottom: 1px solid rgba(0, 0, 0, 0.05);">Computation Type</th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: #333; border-bottom: 1px solid rgba(0, 0, 0, 0.05);">Status</th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: #333; border-bottom: 1px solid rgba(0, 0, 0, 0.05);">Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaveBalances as $balance)
                        @php
                            $computation = \App\Models\LeaveCredit::computeLeaveCredits(Auth::user(), $balance->leaveType);
                        @endphp
                        <tr style="border-bottom: 1px solid rgba(0, 0, 0, 0.05);">
                            <td style="padding: 1rem;">
                                <div style="font-weight: 600; color: #333;">{{ $balance->leaveType->name }}</div>
                                @if($balance->leaveType->is_paid)
                                    <span style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 0.25rem 0.75rem; border-radius: 1rem; font-size: 0.75rem; font-weight: 600;">Paid</span>
                                @else
                                    <span style="background: linear-gradient(135deg, #047857 0%, #065f46 100%); color: white; padding: 0.25rem 0.75rem; border-radius: 1rem; font-size: 0.75rem; font-weight: 600;">Unpaid</span>
                                @endif
                            </td>
                            <td style="padding: 1rem;">
                                <div style="font-size: 1.125rem; font-weight: 600; color: #333;">{{ number_format($computation['total_earned'], 2) }}</div>
                                <div style="font-size: 0.875rem; color: #666; margin-top: 0.25rem;">days</div>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="font-size: 1.125rem; font-weight: 600; color: {{ $computation['balance'] > 0 ? '#10b981' : '#ef4444' }};">{{ number_format($computation['balance'], 2) }}</div>
                                <div style="font-size: 0.875rem; color: #666; margin-top: 0.25rem;">days</div>
                            </td>
                            <td style="padding: 1rem;">
                                <span style="background: rgba(4, 120, 87, 0.1); color: #047857; padding: 0.25rem 0.75rem; border-radius: 1rem; font-size: 0.75rem; font-weight: 600;">{{ ucfirst(str_replace('_', ' ', $computation['computation_type'])) }}</span>
                            </td>
                            <td style="padding: 1rem;">
                                @if($computation['balance'] > 0)
                                    <span style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 0.25rem 0.75rem; border-radius: 1rem; font-size: 0.75rem; font-weight: 600;">Available</span>
                                @else
                                    <span style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; padding: 0.25rem 0.75rem; border-radius: 1rem; font-size: 0.75rem; font-weight: 600;">Exhausted</span>
                                @endif
                            </td>
                            <td style="padding: 1rem;">
                                <div style="font-size: 0.875rem; color: #666;">{{ $balance->as_of_date ? $balance->as_of_date->format('M d, Y') : 'N/A' }}</div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 1rem; padding: 3rem; text-align: center; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <div style="color: #666;">
                <i class="fas fa-balance-scale" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                <h4 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 1rem;">No Leave Balances Found</h4>
                <p style="margin-bottom: 1.5rem;">Your leave balances haven't been computed yet. This could be because:</p>
                <ul style="text-align: left; max-width: 400px; margin: 0 auto; line-height: 1.6;">
                    <li style="margin-bottom: 0.5rem;">Your designation document hasn't been approved yet</li>
                    <li style="margin-bottom: 0.5rem;">Your leave credits haven't been initialized by HR</li>
                    <li style="margin-bottom: 0.5rem;">You're a new employee and system hasn't been set up for you yet</li>
                </ul>
                <div style="margin-top: 2rem;">
                    <a href="{{ route('designation-documents.create') }}" style="background: linear-gradient(135deg, #047857 0%, #065f46 100%); color: white; text-decoration: none; padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-upload"></i>Upload Designation Document
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

<div style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 1rem; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h4 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: #333;">Leave Computation Rules</h4>
    </div>
    <div style="color: #333; line-height: 1.6;">
        <div style="margin-bottom: 1.5rem;">
            <h5 style="color: #047857; font-size: 1.125rem; font-weight: 600; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-user-tie"></i>Faculty With Designation
            </h5>
            <ul style="margin: 0; padding-left: 1.5rem; line-height: 1.8;">
                <li style="margin-bottom: 0.5rem;">Earn 1.25 days per month (15 vacation leave annually)</li>
                <li style="margin-bottom: 0.5rem;">Earn 1.25 days per month (15 sick leave annually)</li>
                <li style="margin-bottom: 0.5rem;">Total: 30 days of leave credits annually</li>
            </ul>
        </div>
        <div>
            <h5 style="color: #f59e0b; font-size: 1.125rem; font-weight: 600; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-user"></i>Faculty Without Designation
            </h5>
            <ul style="margin: 0; padding-left: 1.5rem; line-height: 1.8;">
                <li style="margin-bottom: 0.5rem;">Earn 15 days of service credit per year</li>
                <li style="margin-bottom: 0.5rem;">Subject to documentation requirements</li>
                <li style="margin-bottom: 0.5rem;">Conversion formula: Service Credits × 69 ÷ 30</li>
            </ul>
        </div>
    </div>
</div>
@endsection
