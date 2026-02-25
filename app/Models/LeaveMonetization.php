<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveMonetization extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'credits_monetized',
        'rate_per_credit',
        'total_amount',
        'monetization_date',
        'status',
        'reason',
        'remarks',
        'approved_at',
        'processed_at',
        'approved_by',
        'processed_by',
    ];

    protected $casts = [
        'credits_monetized' => 'decimal:2',
        'rate_per_credit' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'monetization_date' => 'date',
        'approved_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the leave monetization.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the leave type that owns the leave monetization.
     */
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    /**
     * Get the user who approved the monetization.
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who processed the monetization.
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
