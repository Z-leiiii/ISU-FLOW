<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceRecord;
use App\Models\TardinessRecord;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display attendance records.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $currentMonth = $request->get('month', now()->month);
        $currentYear = $request->get('year', now()->year);
        
        // Get attendance records based on user role
        if ($user->hasRole('admin') || $user->hasRole('hr')) {
            // HR/Admin can see all employees' attendance records
            $attendances = AttendanceRecord::with(['user.department'])
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->orderBy('date', 'desc')
                ->orderBy('user_id', 'asc')
                ->get();
                
            $tardinessRecords = TardinessRecord::with(['user.department'])
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->orderBy('date', 'desc')
                ->get();
        } else {
            // Employees can only see their own attendance records
            $attendances = $user->attendanceRecords()
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->orderBy('date', 'desc')
                ->get();

            $tardinessRecords = $user->tardinessRecords()
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->orderBy('date', 'desc')
                ->get();
        }

        // Calculate statistics
        $stats = [
            'total_days' => $attendances->count(),
            'present_days' => $attendances->where('status', 'present')->count(),
            'absent_days' => $attendances->where('status', 'absent')->count(),
            'late_days' => $attendances->where('status', 'late')->count(),
            'half_days' => $attendances->where('status', 'half_day')->count(),
            'total_hours_worked' => $attendances->sum('hours_worked'),
            'tardiness_count' => $tardinessRecords->count(),
            'total_minutes_late' => $tardinessRecords->sum('minutes_late'),
            'average_hours_per_day' => $attendances->count() > 0 ? round($attendances->sum('hours_worked') / $attendances->count(), 2) : 0,
        ];

        // Get available months for filter
        $availableMonths = $this->getAvailableMonths($user);

        return view('attendance.index', compact('attendances', 'tardinessRecords', 'stats', 'currentMonth', 'currentYear', 'availableMonths'));
    }

    /**
     * Store a new attendance record.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Check if user has HR or Admin role
        if (!$user->hasRole('admin') && !$user->hasRole('hr')) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date|unique:attendance_records,date,NULL,user_id,user_id,' . $request->user_id,
            'time_in' => 'required|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i|after:time_in',
            'break_start' => 'nullable|date_format:H:i|after:time_in',
            'break_end' => 'nullable|date_format:H:i|after:break_start',
            'status' => 'required|in:present,absent,late,half_day,holiday,leave',
            'remarks' => 'nullable|string|max:255',
        ]);

        $attendance = new AttendanceRecord([
            'user_id' => $request->user_id,
            'date' => $request->date,
            'time_in' => $request->time_in,
            'time_out' => $request->time_out,
            'break_start' => $request->break_start,
            'break_end' => $request->break_end,
            'status' => $request->status,
            'remarks' => $request->remarks,
        ]);

        // Calculate hours worked
        if ($request->time_in && $request->time_out) {
            $attendance->hours_worked = $this->calculateHoursWorked($attendance);
        }

        $attendance->save();

        // Check for tardiness if status is present or late
        if (in_array($request->status, ['present', 'late']) && $request->time_in) {
            $this->checkAndCreateTardinessRecord($attendance);
        }

        return redirect()->route('attendance.index')
            ->with('success', 'Attendance record added successfully.');
    }

    /**
     * Calculate hours worked based on time in/out and breaks.
     */
    private function calculateHoursWorked($attendance)
    {
        if (!$attendance->time_in || !$attendance->time_out) {
            return 0;
        }

        $timeIn = Carbon::parse($attendance->time_in);
        $timeOut = Carbon::parse($attendance->time_out);
        $totalHours = $timeOut->diffInHours($timeIn);

        // Subtract break time if both break start and end are provided
        if ($attendance->break_start && $attendance->break_end) {
            $breakStart = Carbon::parse($attendance->break_start);
            $breakEnd = Carbon::parse($attendance->break_end);
            $breakHours = $breakEnd->diffInHours($breakStart);
            $totalHours -= $breakHours;
        }

        return max(0, $totalHours);
    }

    /**
     * Check and create tardiness record if applicable.
     */
    private function checkAndCreateTardinessRecord($attendance)
    {
        $scheduledTimeIn = Carbon::parse($attendance->time_in)->setTime(8, 0, 0); // Assuming 8:00 AM start time
        $actualTimeIn = Carbon::parse($attendance->time_in);

        if ($actualTimeIn->gt($scheduledTimeIn)) {
            $minutesLate = $actualTimeIn->diffInMinutes($scheduledTimeIn);
            
            // Determine tardiness status
            $status = 'late';
            if ($minutesLate > 30) {
                $status = 'very_late';
            }
            if ($minutesLate > 60) {
                $status = 'extreme_late';
            }

            TardinessRecord::create([
                'user_id' => $attendance->user_id,
                'date' => $attendance->date,
                'time_in' => $attendance->time_in,
                'scheduled_time_in' => $scheduledTimeIn->format('H:i'),
                'minutes_late' => $minutesLate,
                'status' => $status,
            ]);
        }
    }

    /**
     * Get available months for attendance filter.
     */
    private function getAvailableMonths($user)
    {
        $attendanceDates = $user->attendanceRecords()
            ->orderBy('date', 'asc')
            ->pluck('date')
            ->unique()
            ->map(function ($date) {
                return Carbon::parse($date);
            });

        return $attendanceDates->groupBy(function ($date) {
            return $date->format('Y');
        })->map(function ($dates) {
            return $dates->map(function ($date) {
                return [
                    'month' => $date->month,
                    'month_name' => $date->format('F'),
                    'year' => $date->year,
                    'label' => $date->format('F Y'),
                ];
            })->unique('month');
        })->flatten(1);
    }

    /**
     * Time in functionality for employees.
     */
    public function timeIn(Request $request)
    {
        $user = Auth::user();
        
        // Check if user has HR or Admin role
        if (!$user->hasRole('admin') && !$user->hasRole('hr')) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
        ]);

        $user = Auth::user();
        $dateTime = Carbon::parse($request->date . ' ' . $request->time);

        // Check if already timed in for this date
        $existingAttendance = $user->attendanceRecords()
            ->where('date', $request->date)
            ->whereNotNull('time_in')
            ->first();

        if ($existingAttendance && !$existingAttendance->time_out) {
            return redirect()->back()
                ->with('error', 'You have already timed in for this date. Please time out first.');
        }

        // Create or update attendance record
        if ($existingAttendance) {
            $existingAttendance->update(['time_in' => $request->time]);
        } else {
            $attendance = new AttendanceRecord([
                'user_id' => $user->id,
                'date' => $request->date,
                'time_in' => $request->time,
                'status' => 'present',
            ]);
            $attendance->save();

            // Check for tardiness
            $this->checkAndCreateTardinessRecord($attendance);
        }

        return redirect()->route('attendance.index')
            ->with('success', 'Time in recorded successfully at ' . $request->time);
    }

    /**
     * Time out functionality for employees.
     */
    public function timeOut(Request $request)
    {
        $user = Auth::user();
        
        // Check if user has HR or Admin role
        if (!$user->hasRole('admin') && !$user->hasRole('hr')) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
        ]);

        $user = Auth::user();

        // Find attendance record for this date
        $attendance = $user->attendanceRecords()
            ->where('date', $request->date)
            ->whereNotNull('time_in')
            ->whereNull('time_out')
            ->first();

        if (!$attendance) {
            return redirect()->back()
                ->with('error', 'No time in record found for this date. Please time in first.');
        }

        // Update attendance record with time out
        $attendance->update(['time_out' => $request->time]);

        // Calculate hours worked
        $attendance->hours_worked = $this->calculateHoursWorked($attendance);
        $attendance->save();

        return redirect()->route('attendance.index')
            ->with('success', 'Time out recorded successfully at ' . $request->time . '. Hours worked: ' . number_format($attendance->hours_worked, 2));
    }

    /**
     * Get current attendance status.
     */
    public function currentStatus()
    {
        $user = Auth::user();
        $today = now()->format('Y-m-d');

        $todayAttendance = $user->attendanceRecords()
            ->where('date', $today)
            ->first();

        $status = [
            'date' => $today,
            'has_timed_in' => $todayAttendance && $todayAttendance->time_in,
            'has_timed_out' => $todayAttendance && $todayAttendance->time_out,
            'time_in' => $todayAttendance ? $todayAttendance->time_in : null,
            'time_out' => $todayAttendance ? $todayAttendance->time_out : null,
            'hours_worked' => $todayAttendance ? $todayAttendance->hours_worked : 0,
        ];

        return response()->json($status);
    }
}
