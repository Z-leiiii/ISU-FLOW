<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISU-Flow - Leave Credits Management</title>
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
                            <a href="{{ route('leave-applications.index') }}" class="text-green-200 hover:bg-green-500 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Leave Applications</a>
                            <a href="{{ route('leave-credits.index') }}" class="bg-green-700 text-white px-3 py-2 rounded-md text-sm font-medium">Leave Credits</a>
                            <a href="{{ route('attendance.index') }}" class="text-green-200 hover:bg-green-500 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Attendance</a>
                            <a href="{{ route('reports.index') }}" class="text-green-200 hover:bg-green-500 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Reports</a>
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

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Leave Credits Management</h1>
                        <a href="{{ route('leave-credits.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-green-700">
                            Add Leave Credits
                        </a>
                    </div>

                    @if ($message = Session::get('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ $message }}
                        </div>
                    @endif

                    @if ($message = Session::get('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            {{ $message }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Employee
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Department
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Vacation Leave
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Sick Leave
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Mandatory Leave
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($employees as $employee)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $employee->full_name }}</div>
                                            <div class="text-sm text-gray-500">{{ $employee->employee_id }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $employee->department ? $employee->department->name : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @php
                                                $vacationBalance = $employee->leaveBalances->where('leave_type_id', 1)->first();
                                            @endphp
                                            {{ $vacationBalance ? $vacationBalance->current_balance : 0 }} days
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @php
                                                $sickBalance = $employee->leaveBalances->where('leave_type_id', 2)->first();
                                            @endphp
                                            {{ $sickBalance ? $sickBalance->current_balance : 0 }} days
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @php
                                                $mandatoryBalance = $employee->leaveBalances->where('leave_type_id', 3)->first();
                                            @endphp
                                            {{ $mandatoryBalance ? $mandatoryBalance->current_balance : 0 }} days
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button onclick="showAddCreditsModal({{ $employee->id }})" class="text-green-600 hover:text-green-900">
                                                Add Credits
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            No employees found.
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

    <!-- Add Credits Modal -->
    <div id="addCreditsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Add Leave Credits</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Add leave credits to employee</p>
                </div>
                <form id="addCreditsForm" method="POST" class="mt-4">
                    @csrf
                    <input type="hidden" id="employee_id" name="user_id">
                    <div class="mb-4">
                        <label for="leave_type_id" class="block text-sm font-medium text-gray-700">Leave Type</label>
                        <select id="leave_type_id" name="leave_type_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <option value="">Select Leave Type</option>
                            <option value="1">Vacation Leave</option>
                            <option value="2">Sick Leave</option>
                            <option value="3">Mandatory Leave</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="credits_to_add" class="block text-sm font-medium text-gray-700">Credits to Add</label>
                        <input type="number" id="credits_to_add" name="credits_to_add" min="1" max="365" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>
                    <div class="mb-4">
                        <label for="remarks" class="block text-sm font-medium text-gray-700">Remarks</label>
                        <textarea id="remarks" name="remarks" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"></textarea>
                    </div>
                    <div class="flex items-center justify-between px-4 py-3 bg-gray-50 text-right sm:px-6 sm:rounded-b-md">
                        <button type="button" onclick="hideAddCreditsModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-400 mr-3">
                            Cancel
                        </button>
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-green-700">
                            Add Credits
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showAddCreditsModal(employeeId) {
            document.getElementById('employee_id').value = employeeId;
            document.getElementById('addCreditsModal').classList.remove('hidden');
        }

        function hideAddCreditsModal() {
            document.getElementById('addCreditsModal').classList.add('hidden');
        }

        document.getElementById('addCreditsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const employeeId = formData.get('user_id');
            
            fetch(`/leave-credits/update-balance/${employeeId}/${formData.get('leave_type_id')}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    credits_to_add: formData.get('credits_to_add'),
                    remarks: formData.get('remarks'),
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while adding credits.');
            });
        });
    </script>
</body>
</html>
