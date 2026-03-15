<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     * User relationship
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
     * Compute leave credits
     */
    public static function computeLeaveCredits($user, $leaveType, $asOfDate = null)
    {
        $asOfDate = $asOfDate ?? now();
        $user = User::find($user->id);

        // Check if user has designation
        $hasDesignation = $user->designation && $user->designation->approved_at;

        if ($hasDesignation) {
            // Faculty with designation
            return self::computeDesignationBasedCredits($user, $leaveType, $asOfDate);
        } else {
            // Faculty without designation
            return self::computeServiceBasedCredits($user, $leaveType, $asOfDate);
        }
    }

    /**
     * Faculty WITH designation
     * 1.25 leave per month
     */
    private static function computeDesignationBasedCredits($user, $leaveType, $asOfDate)
    {
        $monthsWorked = self::calculateMonthsWorked($user, $asOfDate);
        $monthlyRate = 1.25;

        $totalEarned = $monthsWorked * $monthlyRate;

        if (!in_array($leaveType->code, ['VL','VACATION','SL','SICK'])) {
            $totalEarned = self::convertServiceCredits($totalEarned);
        }

        return [
            'balance' => min($totalEarned, $leaveType->max_days_per_year ?? 30),
            'computation_type' => 'designation_based',
            'monthly_rate' => $monthlyRate,
            'months_worked' => $monthsWorked
        ];
    }

    /**
     * Faculty WITHOUT designation
     * 15 days per year service credit
     */
    private static function computeServiceBasedCredits($user, $leaveType, $asOfDate)
    {
        $yearsWorked = self::calculateYearsWorked($user, $asOfDate);
        $annualRate = 15;

        $totalEarned = $yearsWorked * $annualRate;

        if (in_array($leaveType->code, ['VL','VACATION','SL','SICK'])) {
            $totalEarned = self::convertServiceCredits($totalEarned);
        }

        return [
            'balance' => min($totalEarned, $leaveType->max_days_per_year ?? 30),
            'computation_type' => 'service_based',
            'annual_rate' => $annualRate,
            'years_worked' => $yearsWorked
        ];
    }

    /**
     * Months worked
     */
    private static function calculateMonthsWorked($user, $asOfDate)
    {
        $startDate = $user->date_hired ?? $user->created_at;
        return $startDate->diffInMonths($asOfDate);
    }

    /**
     * Years worked
     */
    private static function calculateYearsWorked($user, $asOfDate)
    {
        $startDate = $user->date_hired ?? $user->created_at;
        return $startDate->diffInYears($asOfDate);
    }

    /**
     * Convert service credits
     * Formula: ×69 ÷30
     */
    private static function convertServiceCredits($serviceCredits)
    {
        return ($serviceCredits * 69) / 30;
    }

    /**
     * Update or create leave credit record
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

        $leaveCredit = self::updateOrCreate(
            [
                'user_id' => $userId,
                'leave_type_id' => $leaveTypeId
            ],
            [
                'balance' => $computation['balance'],
                'as_of_date' => $asOfDate,
                'remarks' => "Auto-computed: {$computation['computation_type']}"
            ]
        );

        return $leaveCredit;
    }
}
