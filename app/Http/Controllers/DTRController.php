<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DTR;
use App\Models\User;
use App\Models\Notification;
use Carbon\Carbon;

class DTRController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Check if user is HR or Admin for viewing all DTRs
        if ($user->hasRole(['hr', 'admin'])) {
            $dtrs = DTR::with(['user', 'approvedBy'])
                ->when($request->status, function ($query, $status) {
                    return $query->where('status', $status);
                })
                ->when($request->date_from, function ($query, $dateFrom) {
                    return $query->whereDate('date', '>=', $dateFrom);
                })
                ->when($request->date_to, function ($query, $dateTo) {
                    return $query->whereDate('date', '<=', $dateTo);
                })
                ->orderBy('date', 'desc')
                ->paginate(50);
        } else {
            // Faculty can only see their own DTRs
            $dtrs = DTR::forUser($user->id)
                ->when($request->status, function ($query, $status) {
                    return $query->where('status', $status);
                })
                ->when($request->date_from, function ($query, $dateFrom) {
                    return $query->whereDate('date', '>=', $dateFrom);
                })
                ->when($request->date_to, function ($query, $dateTo) {
                    return $query->whereDate('date', '<=', $dateTo);
                })
                ->orderBy('date', 'desc')
                ->paginate(50);
        }

        return view('dtr.index', compact('dtrs'));
    }

    public function create()
    {
        return view('dtr.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'time_in' => 'nullable|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i|after:time_in',
            'break_start' => 'nullable|date_format:H:i|after:time_in|before:time_out',
            'break_end' => 'nullable|date_format:H:i|after:break_start|before:time_out',
            'remarks' => 'nullable|string|max:500'
        ]);

        $user = auth()->user();

        // Check if DTR already exists for this date
        $existingDTR = DTR::where('user_id', $user->id)
            ->where('date', $request->date)
            ->first();

        if ($existingDTR && $existingDTR->status === 'approved') {
            return redirect()->back()
                ->with('error', 'DTR for this date is already approved and cannot be modified.')
                ->withInput();
        }

        $dtr = $existingDTR ?? new DTR();
        $dtr->user_id = $user->id;
        $dtr->date = $request->date;
        $dtr->time_in = $request->time_in;
        $dtr->time_out = $request->time_out;
        $dtr->break_start = $request->break_start;
        $dtr->break_end = $request->break_end;
        $dtr->remarks = $request->remarks;
        $dtr->status = 'submitted';

        // Calculate hours worked
        $dtr->calculateHoursWorked();
        $dtr->save();

        return redirect()->route('dtr.index')
            ->with('success', 'DTR submitted successfully for ' . $dtr->date->format('M d, Y'));
    }

    public function edit($id)
    {
        $dtr = DTR::findOrFail($id);
        
        // Check permissions
        if (auth()->id() !== $dtr->user_id && !auth()->user()->hasRole(['hr', 'admin'])) {
            abort(403);
        }

        if ($dtr->status === 'approved') {
            return redirect()->back()->with('error', 'Cannot edit approved DTR.');
        }

        return view('dtr.edit', compact('dtr'));
    }

    public function update(Request $request, $id)
    {
        $dtr = DTR::findOrFail($id);
        
        // Check permissions
        if (auth()->id() !== $dtr->user_id && !auth()->user()->hasRole(['hr', 'admin'])) {
            abort(403);
        }

        if ($dtr->status === 'approved') {
            return redirect()->back()->with('error', 'Cannot update approved DTR.');
        }

        $request->validate([
            'time_in' => 'nullable|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i|after:time_in',
            'break_start' => 'nullable|date_format:H:i|after:time_in|before:time_out',
            'break_end' => 'nullable|date_format:H:i|after:break_start|before:time_out',
            'remarks' => 'nullable|string|max:500'
        ]);

        $dtr->time_in = $request->time_in;
        $dtr->time_out = $request->time_out;
        $dtr->break_start = $request->break_start;
        $dtr->break_end = $request->break_end;
        $dtr->remarks = $request->remarks;
        $dtr->status = 'submitted';

        // Recalculate hours worked
        $dtr->calculateHoursWorked();
        $dtr->save();

        return redirect()->route('dtr.index')
            ->with('success', 'DTR updated successfully for ' . $dtr->date->format('M d, Y'));
    }

    public function show($id)
    {
        $dtr = DTR::with(['user', 'approvedBy'])->findOrFail($id);
        
        // Check permissions
        if (auth()->id() !== $dtr->user_id && !auth()->user()->hasRole(['hr', 'admin'])) {
            abort(403);
        }

        return view('dtr.show', compact('dtr'));
    }

    public function approve(Request $request, $id)
    {
        if (!auth()->user()->hasRole(['hr', 'admin'])) {
            abort(403);
        }

        $dtr = DTR::findOrFail($id);
        
        $request->validate([
            'remarks' => 'nullable|string|max:500'
        ]);

        $dtr->status = 'approved';
        $dtr->approved_by = auth()->id();
        $dtr->approved_at = now();
        $dtr->remarks = $request->remarks;
        $dtr->save();

        // Create notification for user
        Notification::create([
            'user_id' => $dtr->user_id,
            'title' => 'DTR Approved',
            'message' => 'Your DTR for ' . $dtr->date->format('M d, Y') . ' has been approved.',
            'type' => 'dtr_approved',
            'priority' => 'info',
            'data' => [
                'dtr_id' => $dtr->id,
                'date' => $dtr->date->format('Y-m-d')
            ]
        ]);

        return redirect()->back()->with('success', 'DTR approved successfully.');
    }

    public function reject(Request $request, $id)
    {
        if (!auth()->user()->hasRole(['hr', 'admin'])) {
            abort(403);
        }

        $dtr = DTR::findOrFail($id);
        
        $request->validate([
            'remarks' => 'required|string|max:500'
        ]);

        $dtr->status = 'rejected';
        $dtr->approved_by = auth()->id();
        $dtr->approved_at = now();
        $dtr->remarks = $request->remarks;
        $dtr->save();

        // Create notification for user
        Notification::create([
            'user_id' => $dtr->user_id,
            'title' => 'DTR Rejected',
            'message' => 'Your DTR for ' . $dtr->date->format('M d, Y') . ' has been rejected. Reason: ' . $request->remarks,
            'type' => 'dtr_rejected',
            'priority' => 'warning',
            'data' => [
                'dtr_id' => $dtr->id,
                'date' => $dtr->date->format('Y-m-d'),
                'reason' => $request->remarks
            ]
        ]);

        return redirect()->back()->with('success', 'DTR rejected successfully.');
    }

    public function bulkApprove(Request $request)
    {
        if (!auth()->user()->hasRole(['hr', 'admin'])) {
            abort(403);
        }

        $request->validate([
            'dtr_ids' => 'required|array',
            'dtr_ids.*' => 'exists:d_t_r_s,id',
            'remarks' => 'nullable|string|max:500'
        ]);

        $approved = DTR::whereIn('id', $request->dtr_ids)
            ->where('status', '!=', 'approved')
            ->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'remarks' => $request->remarks
            ]);

        // Create notifications for all affected users
        $dtrs = DTR::whereIn('id', $request->dtr_ids)->get();
        foreach ($dtrs as $dtr) {
            Notification::create([
                'user_id' => $dtr->user_id,
                'title' => 'DTR Approved',
                'message' => 'Your DTR for ' . $dtr->date->format('M d, Y') . ' has been approved.',
                'type' => 'dtr_approved',
                'priority' => 'info',
                'data' => [
                    'dtr_id' => $dtr->id,
                    'date' => $dtr->date->format('Y-m-d')
                ]
            ]);
        }

        return redirect()->back()->with('success', "Bulk approval completed for {$approved} DTR records.");
    }

    public function getPendingSubmissions()
    {
        $user = auth()->user();
        
        // Get pending DTR days for current month
        $currentMonth = Carbon::now();
        $pendingDays = [];

        for ($day = 1; $day <= $currentMonth->daysInMonth; $day++) {
            $date = Carbon::create($currentMonth->year, $currentMonth->month, $day);
            
            // Skip weekends and future dates
            if ($date->isWeekend() || $date->isFuture()) {
                continue;
            }
            
            $dtr = DTR::where('user_id', $user->id)
                ->where('date', $date->format('Y-m-d'))
                ->first();
            
            if (!$dtr || $dtr->status === 'pending') {
                $pendingDays[] = [
                    'date' => $date->format('Y-m-d'),
                    'day_name' => $date->format('l'),
                    'status' => $dtr ? $dtr->status : 'missing'
                ];
            }
        }

        return response()->json([
            'pending_count' => count($pendingDays),
            'pending_days' => $pendingDays
        ]);
    }

    public function export(Request $request)
    {
        if (!auth()->user()->hasRole(['hr', 'admin'])) {
            abort(403);
        }

        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:' . date('Y')
        ]);

        $dtrs = DTR::with(['user'])
            ->whereMonth('date', $request->month)
            ->whereYear('date', $request->year)
            ->orderBy('user_id')
            ->orderBy('date')
            ->get();

        // CSV export logic here
        $filename = "dtr_export_{$request->year}_{$request->month}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\""
        ];

        $callback = function () use ($dtrs) {
            $file = fopen('php://output', 'w');
            
            // CSV header
            fputcsv($file, [
                'Employee ID',
                'Employee Name',
                'Date',
                'Time In',
                'Time Out',
                'Break Start',
                'Break End',
                'Hours Worked',
                'Overtime Hours',
                'Status',
                'Remarks'
            ]);

            foreach ($dtrs as $dtr) {
                fputcsv($file, [
                    $dtr->user->employee_id ?? '',
                    $dtr->user->full_name ?? '',
                    $dtr->date->format('Y-m-d'),
                    $dtr->time_in ?? '',
                    $dtr->time_out ?? '',
                    $dtr->break_start ?? '',
                    $dtr->break_end ?? '',
                    $dtr->hours_worked,
                    $dtr->overtime_hours,
                    $dtr->status,
                    $dtr->remarks ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
