@extends('layouts.app')

@section('title', 'Employee Management')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Employee Management</h3>
        <div class="inline-flex items-center gap-2">
            <span class="badge-success">
                {{ $employees->count() }} Total Employees
            </span>
            <span class="badge-success">
                {{ $employees->where('is_active', true)->count() }} Active
            </span>
        </div>
        <a href="{{ route('employees.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Employee
        </a>
    </div>
    
    <!-- Search and Filters -->
    <div class="flex gap-4 mb-6 flex-wrap">
        <form method="GET" class="flex items-center gap-2 flex-1">
            <input type="text" name="search" placeholder="Search employees..." 
                   value="{{ request('search') }}" class="form-input" style="flex: 1; max-width: 300px;">
            <select name="department" class="form-select" style="width: auto;">
                <option value="">All Departments</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>
                        {{ $department->name }}
                    </option>
                @endforeach
            </select>
            <select name="status" class="form-select" style="width: auto;">
                <option value="">All Status</option>
                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('employees.index') }}" class="btn btn-secondary">Clear</a>
        </form>
    </div>
    
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Employee ID</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Date Hired</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                    <tr>
                        <td>
                            <div class="flex items-center gap-2 font-weight-600">
                                @if($employee->profile_photo)
                                    <img src="{{ asset('storage/' . $employee->profile_photo) }}" 
                                         alt="{{ $employee->full_name }}" 
                                         class="employee-avatar-small">
                                @else
                                    <div class="employee-avatar-placeholder">
                                        {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                                    </div>
                                @endif
                                {{ $employee->full_name }}
                            </div>
                            <div class="text-sm text-gray-600">{{ $employee->email }}</div>
                        </td>
                        <td>
                            <span class="employee-id-badge">
                                {{ $employee->employee_id }}
                            </span>
                        </td>
                        <td>
                            @if($employee->department)
                                <span class="department-badge">
                                    {{ $employee->department->name }}
                                </span>
                            @else
                                <span class="text-gray-600">No Department</span>
                            @endif
                        </td>
                        <td>
                            @if($employee->designation)
                                <div class="font-weight-600">{{ $employee->designation->name }}</div>
                            @else
                                <span class="text-gray-600">No Designation</span>
                            @endif
                        </td>
                        <td>
                            <div class="text-sm text-gray-600">{{ $employee->contact_number }}</div>
                            <div class="text-sm text-gray-600">{{ $employee->address }}</div>
                        </td>
                        <td>
                            @if($employee->is_active)
                                <span class="badge badge-success">
                                    <i class="fas fa-check"></i> Active
                                </span>
                            @else
                                <span class="badge badge-danger">
                                    <i class="fas fa-times"></i> Inactive
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="text-sm text-gray-600">
                                {{ $employee->date_hired ? $employee->date_hired->format('M d, Y') : 'N/A' }}
                            </div>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('employees.show', $employee) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('employees.destroy', $employee) }}" class="inline-form" 
                                      onsubmit="return confirm('Are you sure you want to delete this employee?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center p-8">
                            <div class="text-gray-600">
                                <i class="fas fa-users empty-icon"></i>
                                No employees found.
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
        <h3 class="card-title">Quick Actions</h3>
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <a href="{{ route('employees.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Employee
        </a>
        <a href="{{ route('leave-credits.index') }}" class="btn btn-secondary">
            <i class="fas fa-coins"></i> Leave Credits
        </a>
        <a href="{{ route('hr.leave-applications.index') }}" class="btn btn-secondary">
            <i class="fas fa-file-alt"></i> Leave Applications
        </a>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">
            <i class="fas fa-chart-bar"></i> Reports
        </a>
    </div>
</div>

<div class="card" style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2);">
    <div class="card-header">
        <h3 class="card-title" style="color: #2563eb;">
            <i class="fas fa-info-circle"></i> Employee Statistics
        </h3>
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; color: #333;">
        <div style="text-align: center; padding: 1rem; background: rgba(255, 255, 255, 0.8); border-radius: 0.5rem;">
            <div style="font-size: 1.5rem; font-weight: 700; color: #667eea;">{{ $employees->count() }}</div>
            <div style="font-size: 0.85rem;">Total Employees</div>
        </div>
        <div style="text-align: center; padding: 1rem; background: rgba(255, 255, 255, 0.8); border-radius: 0.5rem;">
            <div style="font-size: 1.5rem; font-weight: 700; color: #10b981;">{{ $employees->where('is_active', true)->count() }}</div>
            <div style="font-size: 0.85rem;">Active</div>
        </div>
        <div style="text-align: center; padding: 1rem; background: rgba(255, 255, 255, 0.8); border-radius: 0.5rem;">
            <div style="font-size: 1.5rem; font-weight: 700; color: #ef4444;">{{ $employees->where('is_active', false)->count() }}</div>
            <div style="font-size: 0.85rem;">Inactive</div>
        </div>
        <div style="text-align: center; padding: 1rem; background: rgba(255, 255, 255, 0.8); border-radius: 0.5rem;">
            <div style="font-size: 1.5rem; font-weight: 700; color: #f59e0b;">{{ $departments->count() }}</div>
            <div style="font-size: 0.85rem;">Departments</div>
        </div>
    </div>
</div>
@endsection
