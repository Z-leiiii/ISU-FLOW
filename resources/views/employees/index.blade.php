@extends('layouts.app')

@section('title', 'Employee Management')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Employee Management</h3>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <span style="background: rgba(34, 197, 94, 0.1); color: #16a34a; padding: 0.25rem 0.75rem; border-radius: 1rem; font-size: 0.85rem;">
                {{ $employees->count() }} Total Employees
            </span>
            <span style="background: rgba(34, 197, 94, 0.1); color: #16a34a; padding: 0.25rem 0.75rem; border-radius: 1rem; font-size: 0.85rem;">
                {{ $employees->where('is_active', true)->count() }} Active
            </span>
        </div>
        <a href="{{ route('employees.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Employee
        </a>
    </div>
    
    <!-- Search and Filters -->
    <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
        <form method="GET" style="display: flex; gap: 0.5rem; align-items: center; flex: 1;">
            <input type="text" name="search" placeholder="Search employees..." 
                   value="{{ request('search') }}" class="form-control" style="flex: 1; max-width: 300px;">
            <select name="department" class="form-control" style="width: auto;">
                <option value="">All Departments</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>
                        {{ $department->name }}
                    </option>
                @endforeach
            </select>
            <select name="status" class="form-control" style="width: auto;">
                <option value="">All Status</option>
                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('employees.index') }}" class="btn btn-secondary">Clear</a>
        </form>
    </div>
    
    <div style="overflow-x: auto;">
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
                            <div style="font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                                @if($employee->profile_photo)
                                    <img src="{{ asset('storage/' . $employee->profile_photo) }}" 
                                         alt="{{ $employee->full_name }}" 
                                         style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                                @else
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.8rem;">
                                        {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                                    </div>
                                @endif
                                {{ $employee->full_name }}
                            </div>
                            <div style="font-size: 0.85rem; color: #666;">{{ $employee->email }}</div>
                        </td>
                        <td>
                            <span style="font-family: monospace; background: rgba(0,0,0,0.05); padding: 0.25rem 0.5rem; border-radius: 0.25rem;">
                                {{ $employee->employee_id }}
                            </span>
                        </td>
                        <td>
                            @if($employee->department)
                                <span style="background: rgba(59, 130, 246, 0.1); color: #2563eb; padding: 0.25rem 0.75rem; border-radius: 1rem; font-size: 0.85rem;">
                                    {{ $employee->department->name }}
                                </span>
                            @else
                                <span style="color: #666;">No Department</span>
                            @endif
                        </td>
                        <td>
                            @if($employee->designation)
                                <div style="font-weight: 600;">{{ $employee->designation->name }}</div>
                            @else
                                <span style="color: #666;">No Designation</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-size: 0.85rem; color: #666;">{{ $employee->contact_number }}</div>
                            <div style="font-size: 0.85rem; color: #666;">{{ $employee->address }}</div>
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
                            <div style="font-size: 0.85rem; color: #666;">
                                {{ $employee->date_hired ? $employee->date_hired->format('M d, Y') : 'N/A' }}
                            </div>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="{{ route('employees.show', $employee) }}" class="btn btn-primary" style="padding: 0.5rem 1rem;">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-secondary" style="padding: 0.5rem 1rem;">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('employees.destroy', $employee) }}" style="display: inline;" 
                                      onsubmit="return confirm('Are you sure you want to delete this employee?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 0.5rem 1rem;">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 2rem;">
                            <div style="color: #666;">
                                <i class="fas fa-users" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
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
