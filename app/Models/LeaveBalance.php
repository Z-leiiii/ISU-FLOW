<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'total_earned',
        'total_used',
        'current_balance',
        'carry_over',
        'year',
    ];

    protected $casts = [
        'total_earned' => 'decimal:2',
        'total_used' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'carry_over' => 'decimal:2',
        'year' => 'integer',
    ];

    /**
     * Get the user that owns the leave balance.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the leave type that owns the leave balance.
     */
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    /**
     * Update the current balance.
     */
    public function updateBalance()
    {
        $this->current_balance = $this->total_earned + $this->carry_over - $this->total_used;
        $this->save();
    }
}
