<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'is_read',
        'read_at',
        'data',
        'priority',
        'expires_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'expires_at' => 'datetime',
        'data' => 'array',
    ];

    /**
     * Get the user that owns the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Scope for unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for high priority notifications.
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }

    /**
     * Scope for active notifications (not expired).
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Get notification icon based on type.
     */
    public function getIconAttribute(): string
    {
        return match($this->type) {
            'leave_balance_low' => 'fas fa-exclamation-triangle',
            'leave_balance_critical' => 'fas fa-exclamation-circle',
            'dtr_pending' => 'fas fa-clock',
            'cto_earned' => 'fas fa-coins',
            'leave_without_pay' => 'fas fa-money-bill-wave',
            'leave_application' => 'fas fa-calendar-alt',
            'leave_approved' => 'fas fa-check-circle',
            'leave_rejected' => 'fas fa-times-circle',
            default => 'fas fa-bell'
        };
    }

    /**
     * Get notification color based on priority.
     */
    public function getColorAttribute(): string
    {
        return match($this->priority) {
            'urgent' => 'danger',
            'high' => 'warning',
            'medium' => 'info',
            'low' => 'secondary',
            default => 'primary'
        };
    }

    /**
     * Check if notification is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Get time ago format.
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Create low balance notification.
     */
    public static function createLowBalanceNotification($userId, $leaveType, $balance): self
    {
        return static::create([
            'user_id' => $userId,
            'title' => 'Low Leave Balance Alert',
            'message' => "Your {$leaveType} balance is critically low: {$balance} days remaining. Please plan your leave accordingly.",
            'type' => 'leave_balance_low',
            'priority' => 'high',
            'data' => [
                'leave_type' => $leaveType,
                'balance' => $balance,
                'alert_type' => 'low_balance'
            ],
            'expires_at' => now()->addDays(7),
        ]);
    }

    /**
     * Create critical balance notification.
     */
    public static function createCriticalBalanceNotification($userId, $leaveType, $balance): self
    {
        return static::create([
            'user_id' => $userId,
            'title' => 'Critical Leave Balance Alert',
            'message' => "URGENT: Your {$leaveType} balance is critically low: {$balance} days remaining. Immediate action required!",
            'type' => 'leave_balance_critical',
            'priority' => 'urgent',
            'data' => [
                'leave_type' => $leaveType,
                'balance' => $balance,
                'alert_type' => 'critical_balance'
            ],
            'expires_at' => now()->addDays(3),
        ]);
    }

    /**
     * Create DTR pending notification.
     */
    public static function createDTRPendingNotification($userId, $pendingDays): self
    {
        return static::create([
            'user_id' => $userId,
            'title' => 'Pending DTR Submission',
            'message' => "You have {$pendingDays} pending DTR submission(s). Please submit your daily time records.",
            'type' => 'dtr_pending',
            'priority' => 'medium',
            'data' => [
                'pending_days' => $pendingDays,
                'alert_type' => 'dtr_pending'
            ],
            'expires_at' => now()->addDays(2),
        ]);
    }

    /**
     * Create CTO earned notification.
     */
    public static function createCTOEarnedNotification($userId, $hours): self
    {
        return static::create([
            'user_id' => $userId,
            'title' => 'CTO Earned',
            'message' => "You have earned {$hours} hours of Compensatory Time Off from overtime work.",
            'type' => 'cto_earned',
            'priority' => 'info',
            'data' => [
                'hours' => $hours,
                'alert_type' => 'cto_earned'
            ],
            'expires_at' => now()->addDays(30),
        ]);
    }

    /**
     * Create leave without pay notification.
     */
    public static function createLeaveWithoutPayNotification($userId, $days): self
    {
        return static::create([
            'user_id' => $userId,
            'title' => 'Leave Without Pay Status',
            'message' => "You have {$days} day(s) of leave without pay recorded. This may affect your benefits.",
            'type' => 'leave_without_pay',
            'priority' => 'warning',
            'data' => [
                'days' => $days,
                'alert_type' => 'leave_without_pay'
            ],
            'expires_at' => now()->addDays(14),
        ]);
    }
}