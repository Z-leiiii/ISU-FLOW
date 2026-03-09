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
<<<<<<< C:/ISU-FLOW/systemF/app/Models/LeaveCredit.php
<<<<<<< C:/ISU-FLOW/systemF/app/Models/LeaveCredit.php
<<<<<<< C:/ISU-FLOW/systemF/app/Models/LeaveCredit.php
<<<<<<< C:/ISU-FLOW/systemF/app/Models/LeaveCredit.php
<<<<<<< C:/ISU-FLOW/systemF/app/Models/LeaveCredit.php
        'total_earned',
        'total_used',
=======
        'balance',
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Models/LeaveCredit.php
=======
        'balance',
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Models/LeaveCredit.php
=======
        'balance',
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Models/LeaveCredit.php
=======
        'balance',
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Models/LeaveCredit.php
=======
        'balance',
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Models/LeaveCredit.php
        'as_of_date',
        'remarks',
    ];

    protected $casts = [
<<<<<<< C:/ISU-FLOW/systemF/app/Models/LeaveCredit.php
<<<<<<< C:/ISU-FLOW/systemF/app/Models/LeaveCredit.php
<<<<<<< C:/ISU-FLOW/systemF/app/Models/LeaveCredit.php
<<<<<<< C:/ISU-FLOW/systemF/app/Models/LeaveCredit.php
<<<<<<< C:/ISU-FLOW/systemF/app/Models/LeaveCredit.php
        'total_earned' => 'decimal:2',
        'total_used' => 'decimal:2',
=======
        'balance' => 'decimal:2',
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Models/LeaveCredit.php
=======
        'balance' => 'decimal:2',
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Models/LeaveCredit.php
=======
        'balance' => 'decimal:2',
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Models/LeaveCredit.php
=======
        'balance' => 'decimal:2',
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Models/LeaveCredit.php
=======
        'balance' => 'decimal:2',
>>>>>>> C:/Users/zaira/.windsurf/worktrees/systemF/systemF-4394a312/app/Models/LeaveCredit.php
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
     * Get the leave type that owns the leave credit.
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
        
        // Check if user has designation
        $hasDesignation = $user->designation && $user->designation->approved_at;
        
        if ($hasDesignation) {
            // Faculty with designation: 1.25 days per month (15 days annually)
            return self::computeDesignationBasedCredits($user, $leaveType, $asOfDate);
        } else {
            // Faculty without designation: 15 days service credit per year
            return self::computeServiceBasedCredits($user, $leaveType, $asOfDate);
        }
    }

    /**
     * Compute credits for faculty with designation
     */
    private static function computeDesignationBasedCredits($user, $leaveType, $asOfDate)
    {
        $monthsWorked = self::calculateMonthsWorked($user, $asOfDate);
        $monthlyRate = 1.25; // 1.25 days per month
        
        // Different computation for vacation vs sick leave
        if (in_array($leaveType->code, ['VL', 'VACATION'])) {
            // Vacation Leave: 15 days annually (1.25 days/month)
            $totalEarned = $monthsWorked * $monthlyRate;
        } elseif (in_array($leaveType->code, ['SL', 'SICK'])) {
            // Sick Leave: 15 days annually (1.25 days/month)
            $totalEarned = $monthsWorked * $monthlyRate;
        } else {
            // Other leave types: use service credit conversion
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
        
        // Service credit: 15 days per year
        $totalEarned = $yearsWorked * $annualRate;
        
        // Apply conversion formula for different leave types
        if (in_array($leaveType->code, ['VL', 'VACATION', 'SL', 'SICK'])) {
            // Convert service credits to specific leave types using formula
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
     * Convert service credits using formula (×69 ÷30)
     */
    private static function convertServiceCredits($serviceCredits)
    {
        // Conversion formula: service credits × 69 ÷ 30
        return ($serviceCredits * 69) / 30;
    }

    /**
     * Update or create leave credits for user
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
        
        // Update existing or create new leave credit
        $leaveCredit = self::where('user_id', $userId)
            ->where('leave_type_id', $leaveTypeId)
            ->first();

        if ($leaveCredit) {
            $leaveCredit->update([
                'balance' => $computation['balance'],
                'as_of_date' => $asOfDate,
                'remarks' => "Auto-computed: {$computation['computation_type']}"
            ]);
        } else {
            $leaveCredit = self::create([
                'user_id' => $userId,
                'leave_type_id' => $leaveTypeId,
                'balance' => $computation['balance'],
                'as_of_date' => $asOfDate,
                'remarks' => "Auto-computed: {$computation['computation_type']}"
            ]);
        }

        return $leaveCredit;
    }
}
