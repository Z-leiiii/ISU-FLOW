<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class DTR extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'time_in',
        'time_out',
        'break_start',
        'break_end',
        'hours_worked',
        'overtime_hours',
        'status',
        'remarks',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'date' => 'date',
        'time_in' => 'datetime:H:i',
        'time_out' => 'datetime:H:i',
        'break_start' => 'datetime:H:i',
        'break_end' => 'datetime:H:i',
        'hours_worked' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function calculateHoursWorked(): void
    {
        if ($this->time_in && $this->time_out) {
            $totalHours = Carbon::parse($this->time_out)->diffInMinutes(Carbon::parse($this->time_in)) / 60;
            
            // Subtract break time if both break start and end are set
            if ($this->break_start && $this->break_end) {
                $breakMinutes = Carbon::parse($this->break_end)->diffInMinutes(Carbon::parse($this->break_start)) / 60;
                $totalHours -= $breakMinutes;
            }
            
            $this->hours_worked = max(0, $totalHours);
            
            // Calculate overtime (hours beyond 8)
            $this->overtime_hours = max(0, $this->hours_worked - 8);
        }
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('date', $year)->whereMonth('date', $month);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'submitted' => 'info',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }

    public function getFormattedHoursWorkedAttribute(): string
    {
        return number_format($this->hours_worked, 2) . ' hrs';
    }

    public function getFormattedOvertimeHoursAttribute(): string
    {
        return number_format($this->overtime_hours, 2) . ' hrs';
    }
}
