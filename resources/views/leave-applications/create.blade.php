@extends('layouts.app')

@section('title', 'Apply for Leave')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-plus"></i> Apply for Leave</h1>
    <a href="{{ route('leave-applications.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Applications
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-file-alt"></i> Leave Application Form</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('leave-applications.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="leave_type_id" class="form-label">Leave Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="leave_type_id" name="leave_type_id" required>
                            <option value="">Select leave type</option>
                            @foreach($leaveTypes as $leaveType)
                                <option value="{{ $leaveType->id }}" 
                                        data-requires-doc="{{ $leaveType->requires_documentation ? 'true' : 'false' }}"
                                        data-is-paid="{{ $leaveType->is_paid ? 'true' : 'false' }}"
                                        data-max-days="{{ $leaveType->max_days_per_year ?? 'unlimited' }}">
                                    {{ $leaveType->name }}
                                    @if(!$leaveType->is_paid)
                                        (Without Pay)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Select the type of leave you are applying for</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       min="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                       min="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reason" name="reason" rows="4" required
                                  placeholder="Please provide a detailed reason for your leave application..."></textarea>
                        <div class="form-text">Minimum 10 characters</div>
                    </div>

                    <div class="mb-3" id="document-upload-section" style="display: none;">
                        <label for="attachment" class="form-label">Supporting Document <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="attachment" name="attachment" 
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <div class="form-text">
                            Accepted formats: PDF, DOC, DOCX, JPG, JPEG, PNG (Max: 2MB)<br>
                            This leave type requires supporting documentation
                        </div>
                    </div>

                    <!-- Leave Balance Check -->
                    <div class="alert alert-info" id="balance-check" style="display: none;">
                        <h6><i class="fas fa-info-circle"></i> Leave Balance Information</h6>
                        <div id="balance-details">
                            <!-- Balance details will be populated here -->
                        </div>
                    </div>

                    <!-- Leave Without Pay Warning -->
                    <div class="alert alert-warning" id="lwop-warning" style="display: none;">
                        <h6><i class="fas fa-exclamation-triangle"></i> Leave Without Pay Notice</h6>
                        <p class="mb-0" id="lwop-message">
                            This application will be marked as leave without pay due to insufficient balance.
                        </p>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('leave-applications.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary" id="submit-btn">
                            <i class="fas fa-paper-plane"></i> Submit Application
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Leave Balances -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-balance-scale"></i> Your Leave Balances</h5>
            </div>
            <div class="card-body">
                @if($leaveBalances->count() > 0)
                    @foreach($leaveBalances as $balance)
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                            <div>
                                <strong>{{ $balance->leaveType->name }}</strong><br>
                                <small class="text-muted">Available: {{ number_format($balance->balance, 1) }} days</small>
                            </div>
                            <div class="text-end">
                                <span class="badge {{ $balance->balance <= 1 ? 'bg-danger' : ($balance->balance <= 3 ? 'bg-warning' : 'bg-success') }}">
                                    {{ number_format($balance->balance, 1) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted text-center">
                        <i class="fas fa-info-circle"></i> No leave balances available.<br>
                        <small>Upload your designation document to start earning leave credits.</small>
                    </p>
                    <div class="text-center">
                        <a href="{{ route('designation-documents.create') }}" class="btn btn-sm btn-primary">
                            Upload Designation
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Application Guidelines -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Application Guidelines</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success"></i>
                        Apply at least 3 days in advance
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success"></i>
                        Provide detailed reason for leave
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success"></i>
                        Upload required documents when needed
                    </li>
                    <li class="mb-0">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        Insufficient balance = Leave without pay
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const leaveTypeSelect = document.getElementById('leave_type_id');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const documentSection = document.getElementById('document-upload-section');
    const balanceCheck = document.getElementById('balance-check');
    const balanceDetails = document.getElementById('balance-details');
    const lwopWarning = document.getElementById('lwop-warning');
    const lwopMessage = document.getElementById('lwop-message');
    
    const leaveBalances = @json($leaveBalances);

    leaveTypeSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const requiresDoc = selectedOption.dataset.requiresDoc === 'true';
        const isPaid = selectedOption.dataset.isPaid === 'true';
        
        // Show/hide document upload section
        documentSection.style.display = requiresDoc ? 'block' : 'none';
        if (requiresDoc) {
            document.querySelector('#attachment').setAttribute('required', 'required');
        } else {
            document.querySelector('#attachment').removeAttribute('required');
        }
        
        // Update balance information
        updateBalanceInfo();
    });

    startDateInput.addEventListener('change', calculateDays);
    endDateInput.addEventListener('change', calculateDays);

    function updateBalanceInfo() {
        const leaveTypeId = leaveTypeSelect.value;
        
        if (!leaveTypeId) {
            balanceCheck.style.display = 'none';
            lwopWarning.style.display = 'none';
            return;
        }

        const balance = leaveBalances.find(b => b.leave_type_id == leaveTypeId);
        const leaveType = Array.from(leaveTypeSelect.options).find(opt => opt.value == leaveTypeId);
        
        if (balance) {
            balanceDetails.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <strong>Total Earned:</strong> ${balance.total_earned} days
                    </div>
                    <div class="col-md-6">
                        <strong>Total Used:</strong> ${balance.total_used} days
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <strong>Current Balance:</strong> 
                        <span class="badge ${balance.balance <= 1 ? 'bg-danger' : (balance.balance <= 3 ? 'bg-warning' : 'bg-success')}">
                            ${balance.balance} days
                        </span>
                    </div>
                </div>
            `;
            balanceCheck.style.display = 'block';
        } else {
            balanceDetails.innerHTML = `
                <p class="text-muted">No balance information available for this leave type.</p>
            `;
            balanceCheck.style.display = 'block';
        }
        
        calculateDays();
    }

    function calculateDays() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);
        const leaveTypeId = leaveTypeSelect.value;
        
        if (!startDate || !endDate || !leaveTypeId || startDate > endDate) {
            lwopWarning.style.display = 'none';
            return;
        }

        // Calculate working days (excluding weekends)
        let workingDays = 0;
        const currentDate = new Date(startDate);
        
        while (currentDate <= endDate) {
            if (currentDate.getDay() !== 0 && currentDate.getDay() !== 6) {
                workingDays++;
            }
            currentDate.setDate(currentDate.getDate() + 1);
        }

        const balance = leaveBalances.find(b => b.leave_type_id == leaveTypeId);
        const leaveType = Array.from(leaveTypeSelect.options).find(opt => opt.value == leaveTypeId);
        const isPaid = leaveType ? leaveType.dataset.isPaid === 'true' : true;
        
        if (!isPaid || !balance || balance.balance < workingDays) {
            lwopWarning.style.display = 'block';
            if (!balance) {
                lwopMessage.textContent = `No balance available for ${leaveType.text}. This application will be marked as leave without pay.`;
            } else {
                lwopMessage.textContent = `You need ${workingDays} days but only have ${balance.balance} days available. This application will be marked as leave without pay.`;
            }
        } else {
            lwopWarning.style.display = 'none';
        }
    }

    // Set minimum date for end date when start date changes
    startDateInput.addEventListener('change', function() {
        endDateInput.min = this.value;
        if (endDateInput.value && endDateInput.value < this.value) {
            endDateInput.value = this.value;
        }
    });
});
</script>
@endsection
