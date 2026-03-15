<?php

namespace App\Services;

use App\Models\CompensatoryTimeOff;
use App\Models\User;
use App\Mail\CTOExpirationAlert;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CTOMonitoringService
{
    /**
     * Check for expiring CTO records and send alerts
     */
    public static function checkExpiringCTO($daysThreshold = 30)
    {
        $alertsSent = 0;
        
        try {
            $expiringSoon = CompensatoryTimeOff::expiringSoon($daysThreshold)
                ->with('user')
                ->get();
            
            foreach ($expiringSoon as $cto) {
                if (self::sendExpirationAlert($cto, $daysThreshold)) {
                    $alertsSent++;
                }
            }
            
            Log::info('CTO expiration alerts sent', [
                'alerts_sent' => $alertsSent,
                'threshold_days' => $daysThreshold
            ]);
            
            return $alertsSent;
        } catch (\Exception $e) {
            Log::error('Failed to check expiring CTO', [
                'error' => $e->getMessage(),
                'threshold_days' => $daysThreshold
            ]);
            return 0;
        }
    }
    
    /**
     * Send expiration alert for a specific CTO record
     */
    private static function sendExpirationAlert($cto, $daysThreshold)
    {
        try {
            $user = $cto->user;
            
            Mail::to($user->email)->send(new CTOExpirationAlert(
                $user,
                $cto,
                $daysThreshold
            ));
            
            Log::info('CTO expiration alert sent', [
                'user_id' => $user->id,
                'cto_id' => $cto->id,
                'days_until_expiration' => $cto->days_until_expiration,
                'remaining_hours' => $cto->remaining_hours
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send CTO expiration alert', [
                'cto_id' => $cto->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Process expired CTO records
     */
    public static function processExpiredCTO()
    {
        $expiredCount = 0;
        
        try {
            $expiredRecords = CompensatoryTimeOff::expired()->get();
            
            foreach ($expiredRecords as $cto) {
                // Mark as expired
                $cto->update([
                    'status' => 'expired',
                    'remarks' => ($cto->remarks ?? '') . ' - Automatically expired on ' . now()->format('Y-m-d')
                ]);
                
                $expiredCount++;
                
                Log::info('CTO record expired', [
                    'user_id' => $cto->user_id,
                    'cto_id' => $cto->id,
                    'remaining_hours' => $cto->remaining_hours,
                    'expired_date' => $cto->expires_at->format('Y-m-d')
                ]);
            }
            
            Log::info('Expired CTO records processed', [
                'expired_count' => $expiredCount
            ]);
            
            return $expiredCount;
        } catch (\Exception $e) {
            Log::error('Failed to process expired CTO', [
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }
    
    /**
     * Get CTO summary for a user
     */
    public static function getCTOSummary($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return null;
        }
        
        $activeCTO = CompensatoryTimeOff::active()
            ->where('user_id', $userId)
            ->get();
        
        $expiringSoon = CompensatoryTimeOff::expiringSoon(30)
            ->where('user_id', $userId)
            ->get();
        
        $expiredCTO = CompensatoryTimeOff::expired()
            ->where('user_id', $userId)
            ->get();
        
        return [
            'user_id' => $userId,
            'total_active_hours' => $activeCTO->sum('remaining_hours'),
            'active_records' => $activeCTO->count(),
            'expiring_soon_hours' => $expiringSoon->sum('remaining_hours'),
            'expiring_soon_records' => $expiringSoon->count(),
            'expired_hours_lost' => $expiredCTO->sum('remaining_hours'),
            'expired_records' => $expiredCTO->count(),
            'next_expiration' => $expiringSoon->min('expires_at'),
            'active_cto_list' => $activeCTO->map(function($cto) {
                return [
                    'id' => $cto->id,
                    'reason' => $cto->reason,
                    'hours_earned' => $cto->hours_earned,
                    'hours_used' => $cto->hours_used,
                    'remaining_hours' => $cto->remaining_hours,
                    'expires_at' => $cto->expires_at ? $cto->expires_at->format('Y-m-d') : null,
                    'days_until_expiration' => $cto->days_until_expiration,
                    'is_expiring_soon' => $cto->is_expiring_soon
                ];
            }),
            'expiring_soon_list' => $expiringSoon->map(function($cto) {
                return [
                    'id' => $cto->id,
                    'reason' => $cto->reason,
                    'remaining_hours' => $cto->remaining_hours,
                    'expires_at' => $cto->expires_at->format('Y-m-d'),
                    'days_until_expiration' => $cto->days_until_expiration
                ];
            })
        ];
    }
    
    /**
     * Create CTO record from overtime work
     */
    public static function createFromOvertime($userId, $hoursWorked, $reason, $workDate, $approvedBy = null)
    {
        try {
            $cto = CompensatoryTimeOff::createCTO(
                $userId,
                $hoursWorked,
                $reason,
                $workDate
            );
            
            if ($approvedBy) {
                $cto->update([
                    'status' => 'approved',
                    'approved_by' => $approvedBy,
                    'approved_at' => now()
                ]);
            }
            
            Log::info('CTO created from overtime', [
                'user_id' => $userId,
                'hours' => $hoursWorked,
                'reason' => $reason,
                'work_date' => $workDate,
                'approved_by' => $approvedBy
            ]);
            
            return $cto;
        } catch (\Exception $e) {
            Log::error('Failed to create CTO from overtime', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Use CTO hours for leave
     */
    public static function useCTOHours($userId, $hoursToUse, $reason = null)
    {
        try {
            $activeCTO = CompensatoryTimeOff::active()
                ->where('user_id', $userId)
                ->orderBy('expires_at', 'asc') // Use expiring first
                ->get();
            
            $totalAvailable = $activeCTO->sum('remaining_hours');
            
            if ($totalAvailable < $hoursToUse) {
                throw new \Exception("Insufficient CTO hours. Available: {$totalAvailable}, Requested: {$hoursToUse}");
            }
            
            $remainingToUse = $hoursToUse;
            $usedRecords = [];
            
            foreach ($activeCTO as $cto) {
                if ($remainingToUse <= 0) {
                    break;
                }
                
                $availableInRecord = $cto->remaining_hours;
                $useFromRecord = min($availableInRecord, $remainingToUse);
                
                $cto->useHours($useFromRecord);
                
                $usedRecords[] = [
                    'cto_id' => $cto->id,
                    'hours_used' => $useFromRecord,
                    'remaining_hours' => $cto->remaining_hours
                ];
                
                $remainingToUse -= $useFromRecord;
            }
            
            Log::info('CTO hours used', [
                'user_id' => $userId,
                'total_hours_used' => $hoursToUse,
                'records_used' => count($usedRecords)
            ]);
            
            return $usedRecords;
        } catch (\Exception $e) {
            Log::error('Failed to use CTO hours', [
                'user_id' => $userId,
                'hours_requested' => $hoursToUse,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Get all expiring CTO for dashboard
     */
    public static function getExpiringSoonForAll($daysThreshold = 30)
    {
        return CompensatoryTimeOff::expiringSoon($daysThreshold)
            ->with('user')
            ->orderBy('expires_at', 'asc')
            ->get()
            ->map(function($cto) {
                return [
                    'user_name' => $cto->user->full_name,
                    'user_email' => $cto->user->email,
                    'remaining_hours' => $cto->remaining_hours,
                    'expires_at' => $cto->expires_at->format('Y-m-d'),
                    'days_until_expiration' => $cto->days_until_expiration,
                    'reason' => $cto->reason
                ];
            });
    }
}
