<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISU-Flow - Leave Credits</title>
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
            <h1 class="text-3xl font-bold text-gray-900">Leave Credits</h1>
            <p class="mt-2 text-gray-600">View your available leave credits and usage history</p>
        </div>

        <!-- Year Selector -->
        <div class="px-4 py-6 sm:px-0">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Leave Credits Summary - {{ now()->year }}</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">Your current leave credit balances for this year</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <label for="year-selector" class="text-sm font-medium text-gray-700">Year:</label>
                            <select id="year-selector" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="{{ now()->year }}" selected>{{ now()->year }}</option>
                                <option value="{{ now()->year - 1 }}">{{ now()->year - 1 }}</option>
                                <option value="{{ now()->year - 2 }}">{{ now()->year - 2 }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Leave Credits Grid -->
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @php
                            $user = Auth::user();
                            $leaveCredits = $user->leaveCredits()->with('leaveType')->where('year', now()->year)->get();
                        @endphp
                        
                        @forelse ($leaveCredits as $credit)
                            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-lg font-medium text-gray-900">{{ $credit->leaveType->name }}</h4>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $credit->leaveType->code }}
                                    </span>
                                </div>
                                
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Total Earned:</span>
                                        <span class="text-sm font-medium text-gray-900">{{ number_format($credit->credits_earned, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Total Used:</span>
                                        <span class="text-sm font-medium text-gray-900">{{ number_format($credit->credits_used, 2) }}</span>
                                    </div>
                                    <div class="border-t pt-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm font-medium text-gray-900">Available Balance:</span>
                                            <span class="text-lg font-bold text-blue-600">{{ number_format($credit->credits_balance, 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                                @if ($credit->remarks)
                                    <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                                        <p class="text-sm text-yellow-800">{{ $credit->remarks }}</p>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="col-span-full text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No leave credits available</h3>
                                <p class="mt-1 text-sm text-gray-500">Leave credits will be assigned by HR.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Leave Usage History -->
        <div class="px-4 py-6 sm:px-0">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Leave Usage</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Your recent leave applications and their status</p>
                    
                    <div class="mt-5">
                        <div class="flow-root">
                            <ul class="-my-5 divide-y divide-gray-200">
                                @php
                                    $recentApplications = $user->leaveApplications()
                                        ->with('leaveType')
                                        ->orderBy('created_at', 'desc')
                                        ->limit(10)
                                        ->get();
                                @endphp
                                
                                @forelse ($recentApplications as $application)
                                    <li class="py-4">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-shrink-0">
                                                <div class="h-8 w-8 rounded-full 
                                                    @if ($application->status == 'approved') bg-green-100
                                                    @elseif ($application->status == 'rejected') bg-red-100
                                                    @elseif ($application->status == 'pending') bg-yellow-100
                                                    @else bg-gray-100
                                                    @endif flex items-center justify-center">
                                                    <svg class="h-5 w-5 
                                                        @if ($application->status == 'approved') text-green-600
                                                        @elseif ($application->status == 'rejected') text-red-600
                                                        @elseif ($application->status == 'pending') text-yellow-600
                                                        @else text-gray-600
                                                        @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        @if ($application->status == 'approved')
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        @elseif ($application->status == 'rejected')
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        @else
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        @endif
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $application->leaveType->name }} - {{ $application->total_days }} days
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    {{ $application->start_date->format('M d, Y') }} - {{ $application->end_date->format('M d, Y') }}
                                                </p>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if ($application->status == 'approved') bg-green-100 text-green-800
                                                    @elseif ($application->status == 'rejected') bg-red-100 text-red-800
                                                    @elseif ($application->status == 'pending') bg-yellow-100 text-yellow-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ ucfirst($application->status) }}
                                                </span>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="py-8 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">No leave usage history</h3>
                                        <p class="mt-1 text-sm text-gray-500">You haven't used any leave credits yet.</p>
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
