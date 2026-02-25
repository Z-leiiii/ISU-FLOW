<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISU-Flow - Attendance Monitoring</title>
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
                            <a href="{{ route('dashboard') }}" class="text-green-200 hover:bg-green-500 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                            @if(!Auth::user()->hasRole('hr') && !Auth::user()->hasRole('admin'))
                                <a href="{{ route('leave-applications.index') }}" class="text-green-200 hover:bg-green-500 hover:text-white px-3 py-2 rounded-md text-sm font-medium">My Applications</a>
                            @endif
                            @if(Auth::user()->hasAnyRole(['admin', 'hr']))
                                <a href="{{ route('admin.leave-applications.index') }}" class="text-green-200 hover:bg-green-500 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Employee Applications</a>
                                <a href="{{ route('leave-credits.index') }}" class="text-green-200 hover:bg-green-500 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Leave Credits</a>
                            @endif
                            <a href="{{ route('attendance.index') }}" class="bg-green-700 text-white px-3 py-2 rounded-md text-sm font-medium">Attendance</a>
                            @if(Auth::user()->hasAnyRole(['admin', 'hr']))
                                <a href="{{ route('reports.index') }}" class="text-green-200 hover:bg-green-500 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Reports</a>
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

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="px-4 py-6 sm:px-0">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Attendance Monitoring</h1>
                    <!-- <p class="mt-2 text-gray-600">Track your daily attendance records</p> -->
                </div>
                @if(Auth::user()->hasAnyRole(['admin', 'hr']))
                    <div class="flex space-x-3">
                        <button onclick="showTimeInModal()" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-green-700">
                            Time In
                        </button>
                        <button onclick="showTimeOutModal()" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                            Time Out
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Month Filter -->
        <div class="px-4 py-3 sm:px-0">
            <div class="bg-white shadow rounded-lg p-4">
                <form method="GET" action="{{ route('attendance.index') }}" class="flex items-center space-x-4">
                    <label for="month" class="text-sm font-medium text-gray-700">Filter Month:</label>
                    <select id="month" name="month" onchange="this.form.submit()" class="border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        @foreach ($availableMonths as $month)
                            <option value="{{ $month['month'] }}" {{ $month['month'] == $currentMonth ? 'selected' : '' }}>
                                {{ $month['label'] }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="year" value="{{ $currentYear }}">
                </form>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="px-4 py-6 sm:px-0">
                <div class="rounded-md bg-green-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="px-4 py-6 sm:px-0">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                <!-- Total Days -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Days</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['total_days'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Present Days -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Present Days</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['present_days'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Late Days -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Late Days</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['late_days'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Hours -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Hours</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_hours_worked'], 1) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Average Hours -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2V3a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Avg Hours/Day</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['average_hours_per_day'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Time Record (HR/Admin Management) -->
        @if(Auth::user()->hasAnyRole(['admin', 'hr']))
        <div class="px-4 py-6 sm:px-0">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Daily Time Record Management</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">Track employee attendance, work hours, and ensure payroll accuracy</p>
                        </div>
                        <div class="flex space-x-3">
                            <button onclick="showAddRecordModal()" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-green-700">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Record
                            </button>
                            <button onclick="exportDailyRecords()" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Export
                            </button>
                        </div>
                    </div>

                    <!-- Daily Time Record Table -->
                    <div class="mt-5">
                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time In</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time Out</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Break</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($attendances as $attendance)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-gray-600">{{ substr($attendance->user->first_name, 0, 1) }}{{ substr($attendance->user->last_name, 0, 1) }}</span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $attendance->user->full_name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $attendance->user->department->name ?? 'No Dept' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $attendance->date->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $attendance->time_in ? $attendance->time_in->format('h:i A') : '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $attendance->time_out ? $attendance->time_out->format('h:i A') : '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($attendance->break_start && $attendance->break_end)
                                                {{ $attendance->break_start->format('h:i A') }} - {{ $attendance->break_end->format('h:i A') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span class="font-medium">{{ number_format($attendance->hours_worked ?? 0, 2) }}</span> hrs
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if ($attendance->status == 'present') bg-green-100 text-green-800
                                                @elseif ($attendance->status == 'absent') bg-red-100 text-red-800
                                                @elseif ($attendance->status == 'late') bg-yellow-100 text-yellow-800
                                                @elseif ($attendance->status == 'half_day') bg-orange-100 text-orange-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($attendance->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button onclick="editRecord({{ $attendance->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                            <button onclick="deleteRecord({{ $attendance->id }})" class="text-red-600 hover:text-red-900">Delete</button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No daily time records found for this period.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Attendance Records (Employee View) -->
        <div class="px-4 py-6 sm:px-0">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Attendance Records - {{ now()->format('F Y') }}</h3>
                    
                    <div class="mt-5">
                        <div class="flow-root">
                            <ul class="-my-5 divide-y divide-gray-200">
                                @forelse ($attendances as $attendance)
                                    <li class="py-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div class="flex-shrink-0">
                                                    <div class="h-8 w-8 rounded-full 
                                                        @if ($attendance->status == 'present') bg-green-100
                                                        @elseif ($attendance->status == 'absent') bg-red-100
                                                        @elseif ($attendance->status == 'late') bg-yellow-100
                                                        @elseif ($attendance->status == 'half_day') bg-orange-100
                                                        @else bg-gray-100
                                                        @endif flex items-center justify-center">
                                                        <svg class="h-5 w-5 
                                                            @if ($attendance->status == 'present') text-green-600
                                                            @elseif ($attendance->status == 'absent') text-red-600
                                                            @elseif ($attendance->status == 'late') text-yellow-600
                                                            @elseif ($attendance->status == 'half_day') text-orange-600
                                                            @else text-gray-600
                                                            @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            @if ($attendance->status == 'present')
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            @elseif ($attendance->status == 'absent')
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            @else
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            @endif
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $attendance->date->format('M d, Y') }} - {{ ucfirst($attendance->status) }}
                                                    </p>
                                                    <p class="text-sm text-gray-500">
                                                        @if ($attendance->time_in && $attendance->time_out)
                                                            {{ $attendance->time_in->format('h:i A') }} - {{ $attendance->time_out->format('h:i A') }}
                                                            ({{ number_format($attendance->hours_worked, 1) }} hours)
                                                        @else
                                                            No time recorded
                                                        @endif
                                                    </p>
                                                    @if ($attendance->remarks)
                                                        <p class="text-sm text-gray-500 mt-1">{{ $attendance->remarks }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="py-8 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">No attendance records</h3>
                                        <p class="mt-1 text-sm text-gray-500">No attendance records found for this month.</p>
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tardiness Records -->
        @if ($tardinessRecords->count() > 0)
            <div class="px-4 py-6 sm:px-0">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Tardiness Records</h3>
                        
                        <div class="mt-5">
                            <div class="flow-root">
                                <ul class="-my-5 divide-y divide-gray-200">
                                    @foreach ($tardinessRecords as $tardiness)
                                        <li class="py-4">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-4">
                                                    <div class="flex-shrink-0">
                                                        <div class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center">
                                                            <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-900">
                                                            {{ $tardiness->date->format('M d, Y') }} - {{ number_format($tardiness->minutes_late, 0) }} minutes late
                                                        </p>
                                                        <p class="text-sm text-gray-500">
                                                            Scheduled: {{ $tardiness->scheduled_time_in }} | Actual: {{ $tardiness->time_in->format('h:i A') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </main>

    <!-- Time In Modal -->
    <div id="timeInModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Time In</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Record your time in for today</p>
                </div>
                <form action="{{ route('attendance.time-in') }}" method="POST" class="mt-4">
                    @csrf
                    <div class="mb-4">
                        <label for="time_in_date" class="block text-sm font-medium text-gray-700">Date</label>
                        <input type="date" id="time_in_date" name="date" value="{{ now()->format('Y-m-d') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>
                    <div class="mb-4">
                        <label for="time_in_time" class="block text-sm font-medium text-gray-700">Time</label>
                        <input type="time" id="time_in_time" name="time" value="{{ now()->format('H:i') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>
                    <div class="flex items-center justify-between px-4 py-3 bg-gray-50 text-right sm:px-6 sm:rounded-b-md">
                        <button type="button" onclick="hideTimeInModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-400 mr-3">
                            Cancel
                        </button>
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-green-700">
                            Time In
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Time Out Modal -->
    <div id="timeOutModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Time Out</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Record your time out for today</p>
                </div>
                <form action="{{ route('attendance.time-out') }}" method="POST" class="mt-4">
                    @csrf
                    <div class="mb-4">
                        <label for="time_out_date" class="block text-sm font-medium text-gray-700">Date</label>
                        <input type="date" id="time_out_date" name="date" value="{{ now()->format('Y-m-d') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>
                    <div class="mb-4">
                        <label for="time_out_time" class="block text-sm font-medium text-gray-700">Time</label>
                        <input type="time" id="time_out_time" name="time" value="{{ now()->format('H:i') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>
                    <div class="flex items-center justify-between px-4 py-3 bg-gray-50 text-right sm:px-6 sm:rounded-b-md">
                        <button type="button" onclick="hideTimeOutModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-400 mr-3">
                            Cancel
                        </button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                            Time Out
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showTimeInModal() {
            document.getElementById('timeInModal').classList.remove('hidden');
        }

        function hideTimeInModal() {
            document.getElementById('timeInModal').classList.add('hidden');
        }

        function showTimeOutModal() {
            document.getElementById('timeOutModal').classList.remove('hidden');
        }

        function hideTimeOutModal() {
            document.getElementById('timeOutModal').classList.add('hidden');
        }

        // Daily Time Record Management Functions
        function showAddRecordModal() {
            // Create modal if it doesn't exist
            if (!document.getElementById('addRecordModal')) {
                const modalHtml = `
                    <div id="addRecordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
                        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                            <div class="mt-3 text-center">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Add Daily Time Record</h3>
                                <div class="mt-2 px-7 py-3">
                                    <p class="text-sm text-gray-500">Add attendance record for an employee</p>
                                </div>
                                <form action="{{ route('attendance.store') }}" method="POST" class="mt-4">
                                    @csrf
                                    <div class="mb-4">
                                        <label for="employee" class="block text-sm font-medium text-gray-700">Employee</label>
                                        <select id="employee" name="user_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                            <option value="">Select Employee</option>
                                            <!-- Employee options will be populated by JavaScript -->
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label for="record_date" class="block text-sm font-medium text-gray-700">Date</label>
                                        <input type="date" id="record_date" name="date" value="{{ now()->format('Y-m-d') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                    </div>
                                    <div class="mb-4">
                                        <label for="record_time_in" class="block text-sm font-medium text-gray-700">Time In</label>
                                        <input type="time" id="record_time_in" name="time_in" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                    </div>
                                    <div class="mb-4">
                                        <label for="record_time_out" class="block text-sm font-medium text-gray-700">Time Out</label>
                                        <input type="time" id="record_time_out" name="time_out" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                    </div>
                                    <div class="mb-4">
                                        <label for="record_status" class="block text-sm font-medium text-gray-700">Status</label>
                                        <select id="record_status" name="status" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                            <option value="present">Present</option>
                                            <option value="absent">Absent</option>
                                            <option value="late">Late</option>
                                            <option value="half_day">Half Day</option>
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label for="record_remarks" class="block text-sm font-medium text-gray-700">Remarks</label>
                                        <textarea id="record_remarks" name="remarks" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"></textarea>
                                    </div>
                                    <div class="flex justify-center space-x-3">
                                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-green-700">Add Record</button>
                                        <button type="button" onclick="hideAddRecordModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-400">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                `;
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                // Load employees
                loadEmployees();
            }
            document.getElementById('addRecordModal').classList.remove('hidden');
        }

        function hideAddRecordModal() {
            document.getElementById('addRecordModal').classList.add('hidden');
        }

        function editRecord(recordId) {
            // Implementation for editing a record
            alert('Edit record functionality coming soon for record ID: ' + recordId);
        }

        function deleteRecord(recordId) {
            if (confirm('Are you sure you want to delete this attendance record?')) {
                // Implementation for deleting a record
                alert('Delete record functionality coming soon for record ID: ' + recordId);
            }
        }

        function exportDailyRecords() {
            // Implementation for exporting records
            const month = document.querySelector('select[name="month"]').value;
            const year = document.querySelector('input[name="year"]').value;
            window.open(`/attendance/export?month=${month}&year=${year}`, '_blank');
        }

        function loadEmployees() {
            // Load employees for the dropdown
            fetch('/api/employees')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('employee');
                    data.forEach(employee => {
                        const option = document.createElement('option');
                        option.value = employee.id;
                        option.textContent = employee.full_name;
                        select.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading employees:', error);
                });
        }

        // Check current status on page load
        document.addEventListener('DOMContentLoaded', function() {
            fetch('{{ route("attendance.current-status") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.has_timed_in && !data.has_timed_out) {
                        // User is currently timed in, show time out button
                        document.querySelector('button[onclick="showTimeInModal()"]').disabled = true;
                        document.querySelector('button[onclick="showTimeInModal()"]').classList.add('opacity-50', 'cursor-not-allowed');
                    }
                });
        });
    </script>
</body>
</html>
