<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISU-Flow - New Leave Application</title>
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
                            <a href="{{ route('leave-applications.index') }}" class="bg-green-700 text-white px-3 py-2 rounded-md text-sm font-medium">Leave Applications</a>
                            @if(Auth::user()->hasAnyRole(['admin', 'hr']))
                                <a href="{{ route('leave-credits.index') }}" class="text-green-200 hover:bg-green-500 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Leave Credits</a>
                            @endif
                            <a href="{{ route('attendance.index') }}" class="text-green-200 hover:bg-green-500 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Attendance</a>
                            @if(Auth::user()->hasAnyRole(['admin', 'hr']))
                                <a href="{{ route('reports.index') }}" class="text-green-200 hover:bg-green-500 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Reports</a>
                            @endif
                            @if(Auth::user()->hasRole('admin'))
                                <a href="{{ route('employees.index') }}" class="text-green-200 hover:bg-green-500 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Employees</a>
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
            <div class="flex items-center">
                <a href="{{ route('leave-applications.index') }}" class="text-blue-600 hover:text-blue-900 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">New Leave Application</h1>
            </div>
            <p class="mt-2 text-gray-600">Submit a new leave application</p>
        </div>

        <!-- Leave Credits Summary -->
        <div class="px-4 py-6 sm:px-0">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Your Leave Credits</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse ($leaveCredits as $credit)
                            <div class="border rounded-lg p-4">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $credit->leaveType->name }}</h4>
                                        <p class="text-sm text-gray-500">{{ $credit->leaveType->code }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-semibold text-blue-600">{{ number_format($credit->credits_balance, 2) }}</p>
                                        <p class="text-xs text-gray-500">days available</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-4">
                                <p class="text-gray-500">No leave credits available</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Application Form -->
        <div class="px-4 py-6 sm:px-0">
            <div class="bg-white shadow rounded-lg">
                <form action="{{ route('leave-applications.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="px-4 py-5 sm:p-6">
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Leave Type -->
                            <div>
                                <label for="leave_type_id" class="block text-sm font-medium text-gray-700">Leave Type</label>
                                <select id="leave_type_id" name="leave_type_id" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="">Select a leave type</option>
                                    @foreach ($leaveTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }} ({{ $type->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('leave_type_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Date Range -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                                    <input type="date" id="start_date" name="start_date" required min="{{ now()->format('Y-m-d') }}" value="{{ old('start_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    @error('start_date')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                                    <input type="date" id="end_date" name="end_date" required min="{{ now()->format('Y-m-d') }}" value="{{ old('end_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    @error('end_date')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Total Days Display -->
                            <div>
                                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-blue-800">
                                                <span id="total-days-display">0</span> days will be deducted from your leave credits.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Reason -->
                            <div>
                                <label for="reason" class="block text-sm font-medium text-gray-700">Reason for Leave</label>
                                <textarea id="reason" name="reason" rows="4" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Please provide a detailed reason for your leave application...">{{ old('reason') }}</textarea>
                                @error('reason')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Attachment -->
                            <div>
                                <label for="attachment" class="block text-sm font-medium text-gray-700">Attachment (Optional)</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="attachment" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                <span>Upload a file</span>
                                                <input id="attachment" name="attachment" type="file" class="sr-only" accept=".pdf,.doc,.docx">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PDF, DOC, DOCX up to 2MB</p>
                                    </div>
                                </div>
                                @error('attachment')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <a href="{{ route('leave-applications.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Cancel
                        </a>
                        <button type="submit" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Submit Application
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Calculate total days when dates change
        document.getElementById('start_date').addEventListener('change', calculateTotalDays);
        document.getElementById('end_date').addEventListener('change', calculateTotalDays);

        function calculateTotalDays() {
            const startDate = new Date(document.getElementById('start_date').value);
            const endDate = new Date(document.getElementById('end_date').value);
            
            if (startDate && endDate && startDate <= endDate) {
                const diffTime = Math.abs(endDate - startDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                document.getElementById('total-days-display').textContent = diffDays;
            } else {
                document.getElementById('total-days-display').textContent = '0';
            }
        }

        // Ensure end date is after start date
        document.getElementById('start_date').addEventListener('change', function() {
            const startDate = this.value;
            const endDateInput = document.getElementById('end_date');
            endDateInput.min = startDate;
            
            if (endDateInput.value && endDateInput.value < startDate) {
                endDateInput.value = startDate;
            }
            calculateTotalDays();
        });
    </script>
</body>
</html>
