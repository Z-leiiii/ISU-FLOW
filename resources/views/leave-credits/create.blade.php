@extends('layouts.app')

@section('title', 'Add Leave Credit')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Add Leave Credit</h3>
        <a href="{{ route('leave-credits.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Leave Credits
        </a>
    </div>
    
    <form method="POST" action="{{ route('leave-credits.store') }}">
        @csrf
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <!-- Employee Selection -->
            <div>
                <label for="user_id" class="form-label">Employee</label>
                <select id="user_id" name="user_id" class="form-control" required>
                    <option value="">Select Employee</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">
                            {{ $employee->full_name }} - {{ $employee->department ? $employee->department->name : 'No Department' }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Leave Type Selection -->
            <div>
                <label for="leave_type_id" class="form-label">Leave Type</label>
                <select id="leave_type_id" name="leave_type_id" class="form-control" required>
                    <option value="">Select Leave Type</option>
                    @foreach($leaveTypes as $leaveType)
                        <option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Balance -->
            <div>
                <label for="balance" class="form-label">Leave Balance (Days)</label>
                <input type="number" id="balance" name="balance" class="form-control" 
                       step="0.5" min="0" max="365" required 
                       placeholder="Enter leave balance">
            </div>
            
            <!-- As of Date -->
            <div>
                <label for="as_of_date" class="form-label">As of Date</label>
                <input type="date" id="as_of_date" name="as_of_date" class="form-control" 
                       value="{{ now()->format('Y-m-d') }}" required>
            </div>
            
            <!-- Year -->
            <div>
                <label for="year" class="form-label">Year</label>
                <input type="number" id="year" name="year" class="form-control" 
                       value="{{ now()->year }}" min="2020" max="{{ now()->year + 1 }}" required>
            </div>
            
            <!-- Remarks -->
            <div style="grid-column: 1 / -1;">
                <label for="remarks" class="form-label">Remarks</label>
                <textarea id="remarks" name="remarks" class="form-control" rows="3" 
                          placeholder="Enter any additional notes or remarks"></textarea>
            </div>
        </div>
        
        <div style="display: flex; gap: 1rem; margin-top: 2rem; justify-content: flex-end;">
            <a href="{{ route('leave-credits.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Leave Credit
            </button>
        </div>
    </form>
</div>

<div class="card" style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2);">
    <div class="card-header">
        <h3 class="card-title" style="color: #2563eb;">
            <i class="fas fa-info-circle"></i> Leave Credit Information
        </h3>
    </div>
    <div style="color: #333; line-height: 1.6;">
        <p><strong>Instructions:</strong></p>
        <ul style="margin: 0; padding-left: 1.5rem;">
            <li>Select the employee whose leave credit you want to update</li>
            <li>Choose the appropriate leave type (Vacation, Sick, Mandatory, etc.)</li>
            <li>Enter the current leave balance in days (can use decimals like 1.5)</li>
            <li>Set the "as of" date for when this balance is effective</li>
            <li>Add optional remarks for reference</li>
        </ul>
        <p style="margin-top: 1rem;"><strong>Note:</strong> Leave credits are used to track employee leave entitlements and are automatically deducted when leave applications are approved.</p>
    </div>
</div>
@endsection
