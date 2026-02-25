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
        'default_credits_per_year',
        'is_paid',
        'requires_documentation',
        'is_accumulable',
        'is_monetizable',
        'is_active',
    ];

    protected $casts = [
        'default_credits_per_year' => 'integer',
        'is_paid' => 'boolean',
        'requires_documentation' => 'boolean',
        'is_accumulable' => 'boolean',
        'is_monetizable' => 'boolean',
        'is_active' => 'boolean',
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

    /**
     * Get the leave balances for the leave type.
     */
    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    /**
     * Get the leave monetizations for the leave type.
     */
    public function leaveMonetizations()
    {
        return $this->hasMany(LeaveMonetization::class);
    }
}
