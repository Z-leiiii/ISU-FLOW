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
        'credits_earned',
        'credits_used',
        'credits_balance',
        'year',
        'remarks',
    ];

    protected $casts = [
        'credits_earned' => 'decimal:2',
        'credits_used' => 'decimal:2',
        'credits_balance' => 'decimal:2',
        'year' => 'integer',
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
