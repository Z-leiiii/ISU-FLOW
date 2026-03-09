@extends('layouts.app')

@section('title', 'Leave History')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-history"></i> Leave History</h1>
    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list"></i> Your Leave Applications</h5>
    </div>
    <div class="card-body">
        @if($applications->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Application No.</th>
                            <th>Leave Type</th>
                            <th>Period</th>
                            <th>Days</th>
                            <th>Status</th>
                            <th>Date Filed</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applications as $application)
                            <tr>
                                <td>{{ $application->application_number }}</td>
                                <td>
                                    {{ $application->leaveType->name }}
                                    @if($application->is_without_pay)
                                        <span class="badge bg-warning ms-1">LWOP</span>
                                    @endif
                                </td>
                                <td>{{ $application->start_date->format('M d, Y') }} - {{ $application->end_date->format('M d, Y') }}</td>
                                <td>{{ $application->number_of_days }}</td>
                                <td>
                                    <span class="badge badge-{{ $application->status }}">
                                        {{ ucfirst($application->status) }}
                                    </span>
                                </td>
                                <td>{{ $application->date_filed->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('leave-applications.show', $application) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
                {{ $applications->links() }}
            </div>
        @else
            <div class="text-center text-muted py-5">
                <i class="fas fa-file-alt fa-3x mb-3"></i>
                <h4>No Leave Applications Found</h4>
                <p>You haven't applied for any leave yet.</p>
                <a href="{{ route('leave-applications.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Apply for Leave
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
