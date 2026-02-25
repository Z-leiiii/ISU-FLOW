<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'time_in',
        'time_out',
        'break_start',
        'break_end',
        'hours_worked',
        'status',
        'remarks',
    ];

    protected $casts = [
        'date' => 'date',
        'time_in' => 'datetime',
        'time_out' => 'datetime',
        'break_start' => 'datetime',
        'break_end' => 'datetime',
        'hours_worked' => 'decimal:2',
    ];

    /**
     * Get the user that owns the attendance record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate hours worked based on time in/out and breaks.
     */
    public function calculateHoursWorked()
    {
        if (!$this->time_in || !$this->time_out) {
            return 0;
        }

        $totalHours = $this->time_out->diffInHours($this->time_in);
        
        if ($this->break_start && $this->break_end) {
            $breakHours = $this->break_end->diffInHours($this->break_start);
            $totalHours -= $breakHours;
        }

        return max(0, $totalHours);
    }
}
