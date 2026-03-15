<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CompensatoryTimeOff extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hours_earned',
        'hours_used',
        'reason',
        'work_date',
        'earned_date',
        'expires_at',
        'status',
        'approved_by',
        'approved_at',
        'remarks',
    ];

    protected $casts = [
        'hours_earned' => 'decimal:2',
        'hours_used' => 'decimal:2',
        'work_date' => 'date',
        'earned_date' => 'date',
        'expires_at' => 'date',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the user that owns the CTO.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get remaining hours
     */
    public function getRemainingHoursAttribute()
    {
        return $this->hours_earned - $this->hours_used;
    }

    /**
     * Check if CTO is expired
     */
    public function getIsExpiredAttribute()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if CTO is expiring soon (within 30 days)
     */
    public function getIsExpiringSoonAttribute()
    {
        if (!$this->expires_at) {
            return false;
        }
        
        return $this->expires_at->diffInDays(now()) <= 30 && $this->expires_at->isFuture();
    }

    /**
     * Get days until expiration
     */
    public function getDaysUntilExpirationAttribute()
    {
        if (!$this->expires_at) {
            return null;
        }
        
        if ($this->expires_at->isPast()) {
            return 0;
        }
        
        return $this->expires_at->diffInDays(now());
    }

    /**
     * Scope for active CTO records
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'approved')
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope for expired CTO records
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<', now());
    }

    /**
     * Scope for expiring soon CTO records
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->where('expires_at', '<=', now()->copy()->addDays($days));
    }

    /**
     * Create CTO record with automatic expiration
     */
    public static function createCTO($userId, $hoursEarned, $reason, $workDate, $validityMonths = 6)
    {
        $earnedDate = now();
        $expiresAt = $earnedDate->copy()->addMonths($validityMonths);
        
        return self::create([
            'user_id' => $userId,
            'hours_earned' => $hoursEarned,
            'hours_used' => 0,
            'reason' => $reason,
            'work_date' => $workDate,
            'earned_date' => $earnedDate,
            'expires_at' => $expiresAt,
            'status' => 'pending',
        ]);
    }

    /**
     * Use CTO hours
     */
    public function useHours($hoursToUse)
    {
        if ($hoursToUse > $this->remaining_hours) {
            throw new \Exception('Insufficient CTO hours available');
        }
        
        $this->hours_used += $hoursToUse;
        $this->save();
        
        return $this->remaining_hours;
    }
}
