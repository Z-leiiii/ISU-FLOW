<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TardinessRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'time_in',
        'scheduled_time_in',
        'minutes_late',
        'status',
        'reason',
        'is_excused',
        'remarks',
    ];

    protected $casts = [
        'date' => 'date',
        'time_in' => 'datetime',
        'scheduled_time_in' => 'datetime',
        'minutes_late' => 'decimal:2',
        'is_excused' => 'boolean',
    ];

    /**
     * Get the user that owns the tardiness record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate minutes late.
     */
    public function calculateMinutesLate()
    {
        if (!$this->time_in || !$this->scheduled_time_in) {
            return 0;
        }

        return $this->time_in->diffInMinutes($this->scheduled_time_in);
    }

    /**
     * Determine tardiness status based on minutes late.
     */
    public function determineStatus()
    {
        $minutes = $this->calculateMinutesLate();
        
        if ($minutes <= 15) {
            return 'late';
        } elseif ($minutes <= 30) {
            return 'very_late';
        } else {
            return 'extreme_late';
        }
    }
}
