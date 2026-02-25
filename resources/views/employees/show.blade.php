<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISU-Flow - Employee Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-green-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-white text-xl font-bold">ISU-Flow</h1>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-10 flex items-baseline space-x-4">
                            <a href="{{ route('dashboard') }}" class="bg-green-700 text-white px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                            <a href="{{ route('leave-applications.index') }}" class="text-green-200 hover:bg-green-500 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Leave Applications</a>
                            @if(Auth::user()->hasAnyRole(['admin', 'hr']))
                                <a href="{{ route('leave-credits.index') }}" class="text-green-200 hover:bg-green-500 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Leave Credits</a>
                            @endif
                            <a href="{{ route('attendance.index') }}" class="text-green-200 hover:bg-green-500 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Attendance</a>
                            @if(Auth::user()->hasAnyRole(['admin', 'hr']))
                                <a href="{{ route('reports.index') }}" class="text-green-200 hover:bg-green-500 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Reports</a>
                            @endif
                            @if(Auth::user()->hasRole('admin'))
                                <a href="{{ route('employees.index') }}" class="bg-green-700 text-white px-3 py-2 rounded-md text-sm font-medium">Employees</a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="ml-3 relative">
                        <div class="flex items-center text-white">
                            <span class="mr-2">{{ Auth::user()->full_name }}</span>
                            <span class="text-sm text-green-200">{{ Auth::user()->position }}</span>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="ml-4 text-green-200 hover:text-white text-sm">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Employee Details</h1>
                        <div>
                            <a href="{{ route('employees.edit', $employee) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 mr-3">
                                Edit Employee
                            </a>
                            <a href="{{ route('employees.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-700">
                                Back to Employees
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h3>
                            <dl class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Employee ID:</dt>
                                    <dd class="text-sm text-gray-900">{{ $employee->employee_id }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Full Name:</dt>
                                    <dd class="text-sm text-gray-900">{{ $employee->full_name }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Email:</dt>
                                    <dd class="text-sm text-gray-900">{{ $employee->email }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Contact Number:</dt>
                                    <dd class="text-sm text-gray-900">{{ $employee->contact_number ?? 'N/A' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Address:</dt>
                                    <dd class="text-sm text-gray-900">{{ $employee->address ?? 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Employment Information</h3>
                            <dl class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Department:</dt>
                                    <dd class="text-sm text-gray-900">{{ $employee->department ? $employee->department->name : 'N/A' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Position:</dt>
                                    <dd class="text-sm text-gray-900">{{ $employee->position }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Employment Type:</dt>
                                    <dd class="text-sm text-gray-900">{{ ucfirst($employee->employment_type) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Hire Date:</dt>
                                    <dd class="text-sm text-gray-900">{{ $employee->hire_date->format('M d, Y') }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Status:</dt>
                                    <dd class="text-sm text-gray-900">
                                        @if ($employee->is_active)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Inactive
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">System Information</h3>
                            <dl class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Role:</dt>
                                    <dd class="text-sm text-gray-900">{{ $employee->roles->pluck('name')->join(', ') ?? 'N/A' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Created At:</dt>
                                    <dd class="text-sm text-gray-900">{{ $employee->created_at->format('M d, Y H:i') }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Last Updated:</dt>
                                    <dd class="text-sm text-gray-900">{{ $employee->updated_at->format('M d, Y H:i') }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Leave Balance</h3>
                            <dl class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Vacation Leave:</dt>
                                    <dd class="text-sm text-gray-900">{{ $employee->leaveBalances()->where('leave_type_id', 1)->first()->credits ?? 0 }} days</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Sick Leave:</dt>
                                    <dd class="text-sm text-gray-900">{{ $employee->leaveBalances()->where('leave_type_id', 2)->first()->credits ?? 0 }} days</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Mandatory Leave:</dt>
                                    <dd class="text-sm text-gray-900">{{ $employee->leaveBalances()->where('leave_type_id', 3)->first()->credits ?? 0 }} days</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
