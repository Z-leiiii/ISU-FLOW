<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'requires_documentation',
        'is_paid',
        'is_active',
        'max_days_per_year',
    ];

    protected $casts = [
        'requires_documentation' => 'boolean',
        'is_paid' => 'boolean',
        'is_active' => 'boolean',
        'max_days_per_year' => 'decimal:2',
    ];

    /**
     * Get the leave credits for the leave type.
     */
    public function leaveCredits()
    {
        return $this->hasMany(LeaveCredit::class);
    }

    /**
     * Get the leave applications for the leave type.
     */
    public function leaveApplications()
    {
        return $this->hasMany(LeaveApplication::class);
    }
}
