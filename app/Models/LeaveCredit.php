<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LeaveCredit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'balance',
        'as_of_date',
        'remarks',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'as_of_date' => 'date',
    ];

    /**
     * Get user that owns leave credit.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Leave type relationship
     */
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    /**
     * Compute leave credits based on designation and leave type
     */
    public static function computeLeaveCredits($user, $leaveType, $asOfDate = null)
    {
        $asOfDate = $asOfDate ?? now();
        $user = User::find($user->id);

        // Check if user has an approved designation
        $hasDesignation = $user->designation && $user->designation->approved_at;

        if ($hasDesignation) {
            return self::computeDesignationBasedCredits($user, $leaveType, $asOfDate);
        } else {
            return self::computeServiceBasedCredits($user, $leaveType, $asOfDate);
        }
    }

    /**
     * Compute credits for faculty with designation
     */
    private static function computeDesignationBasedCredits($user, $leaveType, $asOfDate)
    {
        $monthsWorked = self::calculateMonthsWorked($user, $asOfDate);
        $monthlyRate = 1.25; // 15 days annually = 1.25 per month

        if (in_array($leaveType->code, ['VL','VACATION','SL','SICK'])) {
            $totalEarned = $monthsWorked * $monthlyRate;
        } else {
            $totalEarned = self::convertServiceCredits($monthsWorked * $monthlyRate);
        }

        return [
            'total_earned' => min($totalEarned, $leaveType->max_days_per_year ?? 30),
            'balance' => min($totalEarned, $leaveType->max_days_per_year ?? 30),
            'computation_type' => 'designation_based',
            'monthly_rate' => $monthlyRate,
            'months_worked' => $monthsWorked
        ];
    }

    /**
     * Compute credits for faculty without designation
     */
    private static function computeServiceBasedCredits($user, $leaveType, $asOfDate)
    {
        $yearsWorked = self::calculateYearsWorked($user, $asOfDate);
        $annualRate = 15; // 15 days per year
        $totalEarned = $yearsWorked * $annualRate;

        if (in_array($leaveType->code, ['VL','VACATION','SL','SICK'])) {
            $totalEarned = self::convertServiceCredits($totalEarned);
        }

        return [
            'total_earned' => min($totalEarned, $leaveType->max_days_per_year ?? 30),
            'balance' => min($totalEarned, $leaveType->max_days_per_year ?? 30),
            'computation_type' => 'service_based',
            'annual_rate' => $annualRate,
            'years_worked' => $yearsWorked
        ];
    }

    /**
     * Calculate months worked since user joined
     */
    private static function calculateMonthsWorked($user, $asOfDate)
    {
        $startDate = $user->date_hired ?? $user->created_at;
        return $startDate->diffInMonths($asOfDate);
    }

    /**
     * Calculate years worked since user joined
     */
    private static function calculateYearsWorked($user, $asOfDate)
    {
        $startDate = $user->date_hired ?? $user->created_at;
        return $startDate->diffInYears($asOfDate);
    }

    /**
     * Convert service credits using formula (×69 ÷ 30)
     */
    private static function convertServiceCredits($serviceCredits)
    {
        return ($serviceCredits * 69) / 30;
    }

    /**
     * Update or create leave credit record for a user
     */
    public static function updateUserLeaveCredits($userId, $leaveTypeId, $asOfDate = null)
    {
        $user = User::find($userId);
        $leaveType = LeaveType::find($leaveTypeId);
        $asOfDate = $asOfDate ?? now();

        if (!$user || !$leaveType) {
            return false;
        }

        $computation = self::computeLeaveCredits($user, $leaveType, $asOfDate);

        return self::updateOrCreate(
            ['user_id' => $userId, 'leave_type_id' => $leaveTypeId],
            [
                'balance' => $computation['balance'],
                'as_of_date' => $asOfDate,
                'remarks' => "Auto-computed: {$computation['computation_type']}"
            ]
        );
    }
}