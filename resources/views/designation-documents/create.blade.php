@extends('layouts.app')

@section('title', 'Upload Designation Document')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-upload"></i> Upload Designation Document</h1>
    <a href="{{ route('designation-documents.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to My Designations
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-file-contract"></i> Designation Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('designation-documents.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="designation_id" class="form-label">Designation <span class="text-danger">*</span></label>
                        <select class="form-select" id="designation_id" name="designation_id" required>
                            <option value="">Select your designation</option>
                            @foreach($designations as $designation)
                                <option value="{{ $designation->id }}" 
                                        data-vl="{{ $designation->earns_vacation_leave ? $designation->vacation_leave_rate : 0 }}"
                                        data-sl="{{ $designation->earns_sick_leave ? $designation->sick_leave_rate : 0 }}">
                                    {{ $designation->title }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Select your current position/designation</div>
                    </div>

                    <div class="mb-3">
                        <label for="document_title" class="form-label">Document Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="document_title" name="document_title" 
                               value="Designation Document - {{ date('F d, Y') }}" required>
                        <div class="form-text">A descriptive title for your document</div>
                    </div>

                    <div class="mb-3">
                        <label for="document_file" class="form-label">Document File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="document_file" name="document_file" 
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                        <div class="form-text">
                            Accepted formats: PDF, DOC, DOCX, JPG, JPEG, PNG (Max: 5MB)<br>
                            Please upload your appointment order, designation letter, or similar document
                        </div>
                    </div>

                    <!-- Leave Earning Preview -->
                    <div class="alert alert-info" id="leave-earning-preview" style="display: none;">
                        <h6><i class="fas fa-info-circle"></i> Leave Earning Rates for Selected Designation</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Vacation Leave:</strong> <span id="vl-rate">-</span> days per month
                            </div>
                            <div class="col-md-6">
                                <strong>Sick Leave:</strong> <span id="sl-rate">-</span> days per month
                            </div>
                        </div>
                        <small class="text-muted">These rates will be used to calculate your leave credits once approved.</small>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('designation-documents.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Upload Document
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Upload Guidelines -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Upload Guidelines</h5>
            </div>
            <div class="card-body">
                <h6>Required Documents:</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i> Appointment Order
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i> Designation Letter
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i> Service Record
                    </li>
                    <li class="mb-0">
                        <i class="fas fa-check text-success"></i> Position Papers
                    </li>
                </ul>

                <hr>

                <h6>Important Notes:</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        Documents must be clear and readable
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        Ensure your name and position are visible
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        Documents will be reviewed by HR
                    </li>
                    <li class="mb-0">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        Processing takes 2-3 working days
                    </li>
                </ul>
            </div>
        </div>

        <!-- Current Status -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user"></i> Your Current Status</h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <strong>Name:</strong> {{ Auth::user()->full_name }}
                </div>
                <div class="mb-2">
                    <strong>Employee ID:</strong> {{ Auth::user()->employee_id }}
                </div>
                <div class="mb-2">
                    <strong>Department:</strong> {{ Auth::user()->department->name ?? 'Not assigned' }}
                </div>
                <div class="mb-0">
                    <strong>Current Designation:</strong> 
                    @if(Auth::user()->designation)
                        <span class="badge bg-success">{{ Auth::user()->designation->title }}</span>
                    @else
                        <span class="badge bg-warning">Not set</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const designationSelect = document.getElementById('designation_id');
    const preview = document.getElementById('leave-earning-preview');
    const vlRate = document.getElementById('vl-rate');
    const slRate = document.getElementById('sl-rate');

    designationSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value) {
            const vlRateValue = parseFloat(selectedOption.dataset.vl);
            const slRateValue = parseFloat(selectedOption.dataset.sl);
            
            vlRate.textContent = vlRateValue > 0 ? vlRateValue.toFixed(2) : 'Not applicable';
            slRate.textContent = slRateValue > 0 ? slRateValue.toFixed(2) : 'Not applicable';
            
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }
    });
});
</script>
@endsection
