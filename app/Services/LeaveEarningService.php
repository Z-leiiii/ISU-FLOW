<?php

namespace App\Services;

use App\Models\User;
use App\Models\LeaveCredit;
use App\Models\LeaveType;
use App\Models\Designation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LeaveEarningService
{
    /**
     * Automatically earn leave credits for all faculty with designation
     */
    public static function autoEarnMonthlyCredits($targetMonth = null)
    {
        $targetMonth = $targetMonth ?? now();
        $earningsProcessed = 0;
        
        try {
            // Get all users with approved designations
            $usersWithDesignation = User::whereHas('designation', function($query) {
                $query->whereNotNull('approved_at');
            })->where('is_active', true)->get();
            
            foreach ($usersWithDesignation as $user) {
                $creditsEarned = self::processUserMonthlyEarnings($user, $targetMonth);
                if ($creditsEarned > 0) {
                    $earningsProcessed++;
                }
            }
            
            Log::info('Monthly leave earnings processed', [
                'month' => $targetMonth->format('Y-m'),
                'users_processed' => $earningsProcessed
            ]);
            
            return $earningsProcessed;
        } catch (\Exception $e) {
            Log::error('Failed to process monthly leave earnings', [
                'error' => $e->getMessage(),
                'month' => $targetMonth->format('Y-m')
            ]);
            return 0;
        }
    }
    
    /**
     * Process monthly earnings for a specific user
     */
    private static function processUserMonthlyEarnings($user, $targetMonth)
    {
        $totalCreditsEarned = 0;
        
        // Get leave types that earn monthly credits
        $earningLeaveTypes = LeaveType::where('is_active', true)
            ->whereIn('code', ['VL', 'VACATION', 'SL', 'SICK'])
            ->get();
        
        foreach ($earningLeaveTypes as $leaveType) {
            $creditsEarned = self::calculateProratedEarnings($user, $leaveType, $targetMonth);
            
            if ($creditsEarned > 0) {
                // Update or create leave credit record
                $existingCredit = LeaveCredit::where('user_id', $user->id)
                    ->where('leave_type_id', $leaveType->id)
                    ->where('as_of_date', $targetMonth->copy()->endOfMonth())
                    ->first();
                
                if ($existingCredit) {
                    $existingCredit->update([
                        'balance' => $existingCredit->balance + $creditsEarned,
                        'remarks' => "Monthly earning +{$creditsEarned} days. {$existingCredit->remarks}"
                    ]);
                } else {
                    LeaveCredit::create([
                        'user_id' => $user->id,
                        'leave_type_id' => $leaveType->id,
                        'balance' => $creditsEarned,
                        'as_of_date' => $targetMonth->copy()->endOfMonth(),
                        'remarks' => 'Monthly auto-earning'
                    ]);
                }
                
                $totalCreditsEarned += $creditsEarned;
            }
        }
        
        return $totalCreditsEarned;
    }
    
    /**
     * Calculate prorated earnings for mid-month designation changes
     */
    public static function calculateProratedEarnings($user, $leaveType, $targetMonth)
    {
        $monthlyRate = 1.25; // 1.25 days per month
        $daysInMonth = $targetMonth->daysInMonth;
        $designation = $user->designation;
        
        if (!$designation || !$designation->approved_at) {
            return 0;
        }
        
        $approvalDate = Carbon::parse($designation->approved_at);
        $monthStart = $targetMonth->copy()->startOfMonth();
        $monthEnd = $targetMonth->copy()->endOfMonth();
        
        // If designation was approved before this month, full earning
        if ($approvalDate < $monthStart) {
            return $monthlyRate;
        }
        
        // If designation was approved in this month, prorate
        if ($approvalDate >= $monthStart && $approvalDate <= $monthEnd) {
            $daysWithDesignation = $monthEnd->diffInDays($approvalDate) + 1;
            $proratedRate = ($daysWithDesignation / $daysInMonth) * $monthlyRate;
            
            Log::info('Prorated earning calculated', [
                'user_id' => $user->id,
                'month' => $targetMonth->format('Y-m'),
                'days_with_designation' => $daysWithDesignation,
                'days_in_month' => $daysInMonth,
                'prorated_rate' => $proratedRate
            ]);
            
            return round($proratedRate, 2);
        }
        
        // If designation was approved after this month, no earning
        return 0;
    }
    
    /**
     * Handle designation change mid-month
     */
    public static function handleDesignationChange($user, $oldDesignation, $newDesignation, $changeDate)
    {
        $changeDate = Carbon::parse($changeDate);
        $currentMonth = $changeDate->copy()->startOfMonth();
        
        try {
            // Recalculate earnings for the current month
            $earningLeaveTypes = LeaveType::where('is_active', true)
                ->whereIn('code', ['VL', 'VACATION', 'SL', 'SICK'])
                ->get();
            
            foreach ($earningLeaveTypes as $leaveType) {
                // Remove previous earnings for this month
                LeaveCredit::where('user_id', $user->id)
                    ->where('leave_type_id', $leaveType->id)
                    ->where('as_of_date', $currentMonth->copy()->endOfMonth())
                    ->delete();
                
                // Recalculate with new designation
                $newEarnings = self::calculateProratedEarnings($user, $leaveType, $currentMonth);
                
                if ($newEarnings > 0) {
                    LeaveCredit::create([
                        'user_id' => $user->id,
                        'leave_type_id' => $leaveType->id,
                        'balance' => $newEarnings,
                        'as_of_date' => $currentMonth->copy()->endOfMonth(),
                        'remarks' => "Recomputed due to designation change on {$changeDate->format('Y-m-d')}"
                    ]);
                }
            }
            
            Log::info('Designation change handled', [
                'user_id' => $user->id,
                'change_date' => $changeDate->format('Y-m-d'),
                'old_designation' => $oldDesignation ? $oldDesignation->id : null,
                'new_designation' => $newDesignation ? $newDesignation->id : null
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to handle designation change', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Get earning summary for a user
     */
    public static function getEarningSummary($userId, $months = 12)
    {
        $user = User::find($userId);
        if (!$user) {
            return null;
        }
        
        $summary = [
            'user_id' => $userId,
            'monthly_rate' => 1.25,
            'designation_status' => $user->designation && $user->designation->approved_at ? 'active' : 'inactive',
            'months_analyzed' => $months,
            'earnings_by_month' => [],
            'total_earned' => 0,
            'leave_types' => []
        ];
        
        $startDate = now()->copy()->subMonths($months)->startOfMonth();
        $endDate = now()->copy()->endOfMonth();
        
        $leaveCredits = LeaveCredit::where('user_id', $userId)
            ->whereBetween('as_of_date', [$startDate, $endDate])
            ->with('leaveType')
            ->orderBy('as_of_date')
            ->get();
        
        foreach ($leaveCredits as $credit) {
            $monthKey = $credit->as_of_date->format('Y-m');
            
            if (!isset($summary['earnings_by_month'][$monthKey])) {
                $summary['earnings_by_month'][$monthKey] = [
                    'month' => $credit->as_of_date->format('F Y'),
                    'earnings' => 0,
                    'leave_types' => []
                ];
            }
            
            $summary['earnings_by_month'][$monthKey]['earnings'] += $credit->balance;
            $summary['earnings_by_month'][$monthKey]['leave_types'][] = [
                'type' => $credit->leaveType->name,
                'amount' => $credit->balance,
                'remarks' => $credit->remarks
            ];
            
            $summary['total_earned'] += $credit->balance;
            
            if (!isset($summary['leave_types'][$credit->leaveType->name])) {
                $summary['leave_types'][$credit->leaveType->name] = 0;
            }
            $summary['leave_types'][$credit->leaveType->name] += $credit->balance;
        }
        
        return $summary;
    }
}
