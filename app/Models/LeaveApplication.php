<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'application_number',
        'date_filed',
        'start_date',
        'end_date',
        'number_of_days',
        'reason',
        'status',
        'is_without_pay',
        'hr_remarks',
        'department_head_remarks',
        'recommended_at',
        'approved_at',
        'disapproved_at',
        'recommended_by',
        'approved_by',
        'disapproved_by',
        'document_path',
    ];

    protected $casts = [
        'date_filed' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'number_of_days' => 'integer',
        'is_without_pay' => 'boolean',
        'recommended_at' => 'datetime',
        'approved_at' => 'datetime',
        'disapproved_at' => 'datetime',
    ];

    /**
     * Get the user that owns the leave application.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the leave type that owns the leave application.
     */
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    /**
     * Get the user who recommended the leave application.
     */
    public function recommendedBy()
    {
        return $this->belongsTo(User::class, 'recommended_by');
    }

    /**
     * Get the user who approved the leave application.
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who disapproved the leave application.
     */
    public function disapprovedBy()
    {
        return $this->belongsTo(User::class, 'disapproved_by');
    }
}
