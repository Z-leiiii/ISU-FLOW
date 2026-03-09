@extends('layouts.app')

@section('title', 'Designation Document Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-file-contract"></i> Designation Document Details</h1>
    <a href="{{ route('designation-documents.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Documents
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Document Information</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6>Document Details</h6>
                <table class="table table-sm">
                    <tr>
                        <th>Title:</th>
                        <td>{{ $designationDocument->document_title }}</td>
                    </tr>
                    <tr>
                        <th>Designation:</th>
                        <td>{{ $designationDocument->designation->title }}</td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            <span class="badge badge-{{ $designationDocument->status }}">
                                {{ ucfirst($designationDocument->status) }}
                            </span>
                            @if($designationDocument->is_current)
                                <span class="badge bg-info ms-1">Current</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Uploaded Date:</th>
                        <td>{{ $designationDocument->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                    @if($designationDocument->approved_at)
                        <tr>
                            <th>Approved Date:</th>
                            <td>{{ $designationDocument->approved_at->format('M d, Y h:i A') }}</td>
                        </tr>
                    @endif
                </table>
            </div>
            <div class="col-md-6">
                <h6>Leave Earning Rates</h6>
                <table class="table table-sm">
                    <tr>
                        <th>Vacation Leave:</th>
                        <td>
                            @if($designationDocument->designation->earns_vacation_leave)
                                {{ $designationDocument->designation->vacation_leave_rate }} days/month
                            @else
                                Not Applicable
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Sick Leave:</th>
                        <td>
                            @if($designationDocument->designation->earns_sick_leave)
                                {{ $designationDocument->designation->sick_leave_rate }} days/month
                            @else
                                Not Applicable
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        @if($designationDocument->hr_remarks)
            <div class="row mt-3">
                <div class="col-12">
                    <h6>HR Remarks</h6>
                    <div class="alert alert-info">
                        {{ $designationDocument->hr_remarks }}
                    </div>
                </div>
            </div>
        @endif

        <div class="row mt-3">
            <div class="col-12">
                <h6>Actions</h6>
                <div class="btn-group">
                    <a href="{{ route('designation-documents.download', $designationDocument) }}" class="btn btn-primary">
                        <i class="fas fa-download"></i> Download Document
                    </a>
                    @if($designationDocument->status === 'pending')
                        <a href="{{ route('designation-documents.edit', $designationDocument) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('designation-documents.destroy', $designationDocument) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this document?')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
