<?php

namespace App\Services;

use App\Models\User;
use App\Models\LeaveCredit;
use App\Models\LeaveType;
use Carbon\Carbon;

class LeaveCreditService
{
    /**
     * Update all leave credits for a user based on designation
     */
    public static function updateUserAllLeaveCredits($userId, $asOfDate = null)
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        $asOfDate = $asOfDate ?? now();
        $leaveTypes = LeaveType::where('is_active', true)->get();
        $updatedCredits = [];

        foreach ($leaveTypes as $leaveType) {
            $credit = LeaveCredit::updateUserLeaveCredits($userId, $leaveType->id, $asOfDate);
            if ($credit) {
                $updatedCredits[] = $credit;
            }
        }

        return $updatedCredits;
    }

    /**
     * Compute leave balance for display
     */
    public static function computeLeaveBalance($userId, $leaveTypeId, $asOfDate = null)
    {
        $user = User::find($userId);
        $leaveType = LeaveType::find($leaveTypeId);
        
        if (!$user || !$leaveType) {
            return null;
        }

        return LeaveCredit::computeLeaveCredits($user, $leaveType, $asOfDate);
    }

    /**
     * Get leave computation details for user
     */
    public static function getLeaveComputationDetails($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return null;
        }

        $hasDesignation = $user->designation && $user->designation->approved_at;
        $asOfDate = now();
        
        return [
            'user_id' => $userId,
            'has_designation' => $hasDesignation,
            'designation_name' => $hasDesignation ? $user->designation->name : 'None',
            'date_hired' => $user->date_hired,
            'months_worked' => LeaveCredit::calculateMonthsWorked($user, $asOfDate),
            'years_worked' => LeaveCredit::calculateYearsWorked($user, $asOfDate),
            'computation_type' => $hasDesignation ? 'designation_based' : 'service_based',
            'monthly_rate' => $hasDesignation ? 1.25 : null,
            'annual_rate' => $hasDesignation ? null : 15,
            'conversion_formula' => $hasDesignation ? null : '×69 ÷ 30',
        ];
    }

    /**
     * Batch update all users' leave credits
     */
    public static function batchUpdateAllUsers($asOfDate = null)
    {
        $users = User::where('is_active', true)->get();
        $updatedUsers = [];

        foreach ($users as $user) {
            $credits = self::updateUserAllLeaveCredits($user->id, $asOfDate);
            if ($credits) {
                $updatedUsers[] = [
                    'user' => $user,
                    'credits' => $credits
                ];
            }
        }

        return $updatedUsers;
    }

    /**
     * Validate leave application against available balance
     */
    public static function validateLeaveBalance($userId, $leaveTypeId, $requestedDays)
    {
        $computation = self::computeLeaveBalance($userId, $leaveTypeId);
        
        if (!$computation) {
            return [
                'valid' => false,
                'message' => 'Invalid leave type or user not found'
            ];
        }

        $balance = $computation['balance'];
        
        if ($requestedDays > $balance) {
            return [
                'valid' => false,
                'message' => "Insufficient leave balance. Available: {$balance} days, Requested: {$requestedDays} days"
            ];
        }

        return [
            'valid' => true,
            'balance' => $balance,
            'requested' => $requestedDays,
            'remaining' => $balance - $requestedDays
        ];
    }
}
