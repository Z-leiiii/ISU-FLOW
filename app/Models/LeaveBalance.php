<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

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
        'last_updated',
        'computed_at',
    ];

    protected $casts = [
        'total_earned' => 'decimal:2',
        'total_used' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'carry_over' => 'decimal:2',
        'year' => 'integer',
        'last_updated' => 'datetime',
        'computed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the leave balance.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the leave type that owns the leave balance.
     */
    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    /**
     * Update the current balance.
     */
    public function updateBalance(): void
    {
        $this->current_balance = $this->total_earned + $this->carry_over - $this->total_used;
        $this->last_updated = now();
        $this->save();
    }

    /**
     * Get real-time balance computation.
     */
    public function getRealTimeBalance(): array
    {
        $computation = LeaveCredit::computeLeaveCredits($this->user, $this->leaveType);
        
        return [
            'total_earned' => $computation['total_earned'],
            'total_used' => $computation['total_used'],
            'current_balance' => $computation['balance'],
            'computation_type' => $computation['computation_type'],
            'monthly_rate' => $computation['monthly_rate'] ?? null,
            'annual_rate' => $computation['annual_rate'] ?? null,
            'last_updated' => now(),
        ];
    }

    /**
     * Check if balance is low (less than 3 days).
     */
    public function isLowBalance(): bool
    {
        return $this->current_balance < 3;
    }

    /**
     * Check if balance is critical (less than 1 day).
     */
    public function isCriticalBalance(): bool
    {
        return $this->current_balance < 1;
    }

    /**
     * Get balance status with color coding.
     */
    public function getBalanceStatus(): array
    {
        $balance = $this->current_balance;
        
        if ($balance < 1) {
            return [
                'status' => 'critical',
                'color' => 'danger',
                'message' => 'Critical: Less than 1 day remaining',
                'percentage' => max(0, ($balance / 15) * 100),
            ];
        } elseif ($balance < 3) {
            return [
                'status' => 'low',
                'color' => 'warning',
                'message' => 'Low: Less than 3 days remaining',
                'percentage' => max(0, ($balance / 15) * 100),
            ];
        } elseif ($balance < 5) {
            return [
                'status' => 'moderate',
                'color' => 'info',
                'message' => 'Moderate balance',
                'percentage' => ($balance / 15) * 100,
            ];
        } else {
            return [
                'status' => 'good',
                'color' => 'success',
                'message' => 'Good balance',
                'percentage' => min(100, ($balance / 15) * 100),
            ];
        }
    }

    /**
     * Get CTO (Compensatory Time Off) balance.
     */
    public function getCTOBalance(): float
    {
        if ($this->leaveType->name !== 'CTO') {
            return 0;
        }

        // Calculate CTO based on overtime hours from DTR
        $overtimeHours = DTR::forUser($this->user_id)
            ->forYear($this->year)
            ->approved()
            ->sum('overtime_hours');

        return $overtimeHours;
    }

    /**
     * Scope for current year balances.
     */
    public function scopeCurrentYear($query)
    {
        return $query->where('year', date('Y'));
    }

    /**
     * Scope for low balance alerts.
     */
    public function scopeLowBalance($query)
    {
        return $query->where('current_balance', '<', 3);
    }

    /**
     * Scope for critical balance alerts.
     */
    public function scopeCriticalBalance($query)
    {
        return $query->where('current_balance', '<', 1);
    }

    /**
     * Get formatted balance display.
     */
    public function getFormattedBalanceAttribute(): string
    {
        return number_format($this->current_balance, 2) . ' days';
    }

    /**
     * Get balance trend (increasing/decreasing/stable).
     */
    public function getBalanceTrend(): string
    {
        $lastMonth = Carbon::now()->subMonth();
        $lastMonthBalance = static::where('user_id', $this->user_id)
            ->where('leave_type_id', $this->leave_type_id)
            ->where('year', $lastMonth->year)
            ->first();

        if (!$lastMonthBalance) {
            return 'stable';
        }

        if ($this->current_balance > $lastMonthBalance->current_balance) {
            return 'increasing';
        } elseif ($this->current_balance < $lastMonthBalance->current_balance) {
            return 'decreasing';
        }

        return 'stable';
    }
}
