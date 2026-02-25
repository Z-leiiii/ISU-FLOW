<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'leave_application_id',
        'approver_id',
        'level',
        'status',
        'comments',
        'approved_at',
        'rejected_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Get the leave application that owns the approval.
     */
    public function leaveApplication()
    {
        return $this->belongsTo(LeaveApplication::class);
    }

    /**
     * Get the approver that owns the approval.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
