@extends('layouts.app')

@section('title', 'Edit Designation Document')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-edit"></i> Edit Designation Document</h1>
    <a href="{{ route('designation-documents.show', $designationDocument) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Document
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-file-contract"></i> Edit Document Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('designation-documents.update', $designationDocument) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="designation_id" class="form-label">Designation <span class="text-danger">*</span></label>
                        <select class="form-select" id="designation_id" name="designation_id" required>
                            <option value="">Select your designation</option>
                            @foreach($designations as $designation)
                                <option value="{{ $designation->id }}" 
                                        {{ $designationDocument->designation_id == $designation->id ? 'selected' : '' }}
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
                               value="{{ $designationDocument->document_title }}" required>
                        <div class="form-text">A descriptive title for your document</div>
                    </div>

                    <div class="mb-3">
                        <label for="document_file" class="form-label">Document File</label>
                        <input type="file" class="form-control" id="document_file" name="document_file" 
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <div class="form-text">
                            Accepted formats: PDF, DOC, DOCX, JPG, JPEG, PNG (Max: 5MB)<br>
                            Leave empty to keep the current file
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
                        <a href="{{ route('designation-documents.show', $designationDocument) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Document
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Current Document Info -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Current Document</h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <strong>Title:</strong> {{ $designationDocument->document_title }}
                </div>
                <div class="mb-2">
                    <strong>Designation:</strong> {{ $designationDocument->designation->title }}
                </div>
                <div class="mb-2">
                    <strong>Status:</strong> 
                    <span class="badge badge-{{ $designationDocument->status }}">
                        {{ ucfirst($designationDocument->status) }}
                    </span>
                </div>
                <div class="mb-2">
                    <strong>Uploaded:</strong> {{ $designationDocument->created_at->format('M d, Y') }}
                </div>
                <div class="mb-0">
                    <strong>Current File:</strong><br>
                    <a href="{{ route('designation-documents.download', $designationDocument) }}" class="btn btn-sm btn-outline-primary mt-1">
                        <i class="fas fa-download"></i> Download Current File
                    </a>
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

    // Show preview on load if designation is selected
    if (designationSelect.value) {
        updatePreview();
    }

    designationSelect.addEventListener('change', updatePreview);

    function updatePreview() {
        const selectedOption = designationSelect.options[designationSelect.selectedIndex];
        
        if (designationSelect.value) {
            const vlRateValue = parseFloat(selectedOption.dataset.vl);
            const slRateValue = parseFloat(selectedOption.dataset.sl);
            
            vlRate.textContent = vlRateValue > 0 ? vlRateValue.toFixed(2) : 'Not applicable';
            slRate.textContent = slRateValue > 0 ? slRateValue.toFixed(2) : 'Not applicable';
            
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }
    }
});
</script>
@endsection
