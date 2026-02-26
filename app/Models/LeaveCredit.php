<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveCredit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'total_earned',
        'total_used',
        'as_of_date',
        'remarks',
    ];

    protected $casts = [
        'total_earned' => 'decimal:2',
        'total_used' => 'decimal:2',
        'as_of_date' => 'date',
    ];

    /**
     * Get the user that owns the leave credit.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the leave type that owns the leave credit.
     */
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }
}
