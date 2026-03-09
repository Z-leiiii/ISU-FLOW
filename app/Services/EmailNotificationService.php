<?php

namespace App\Services;

use App\Models\User;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Mail\LeaveBalanceInsufficient;
use App\Mail\LeaveApplicationStatusUpdate;
use App\Mail\LeaveApplicationSubmitted;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailNotificationService
{
    /**
     * Send insufficient leave balance notification
     */
    public static function sendInsufficientBalanceNotification($user, $leaveType, $requestedDays, $availableBalance)
    {
        try {
            $computationDetails = LeaveCreditService::getLeaveComputationDetails($user->id);
            
            Mail::to($user->email)->send(new LeaveBalanceInsufficient(
                $user,
                $leaveType,
                $requestedDays,
                $availableBalance,
                $computationDetails
            ));

            Log::info('Insufficient balance notification sent', [
                'user_id' => $user->id,
                'leave_type' => $leaveType->name,
                'requested' => $requestedDays,
                'available' => $availableBalance
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send insufficient balance notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send leave application status update notification
     */
    public static function sendApplicationStatusNotification($leaveApplication, $status, $remarks = null, $processedBy = null)
    {
        try {
            $user = $leaveApplication->user;
            
            Mail::to($user->email)->send(new LeaveApplicationStatusUpdate(
                $user,
                $leaveApplication,
                $status,
                $remarks,
                $processedBy
            ));

            Log::info('Application status notification sent', [
                'application_id' => $leaveApplication->id,
                'user_id' => $user->id,
                'status' => $status
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send application status notification', [
                'application_id' => $leaveApplication->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send new leave application notification to HR
     */
    public static function sendNewApplicationNotification($leaveApplication)
    {
        try {
            $user = $leaveApplication->user;
            
            // Get HR and Admin users
            $hrUsers = User::whereHas('roles', function($query) {
                $query->whereIn('name', ['hr', 'admin']);
            })->get();

            foreach ($hrUsers as $hrUser) {
                Mail::to($hrUser->email)->send(new LeaveApplicationSubmitted(
                    $user,
                    $leaveApplication
                ));
            }

            Log::info('New application notifications sent', [
                'application_id' => $leaveApplication->id,
                'applicant_id' => $user->id,
                'hr_notified' => $hrUsers->count()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send new application notification', [
                'application_id' => $leaveApplication->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send leave balance warning notification
     */
    public static function sendLeaveBalanceWarning($user, $leaveType, $balance)
    {
        try {
            $computationDetails = LeaveCreditService::getLeaveComputationDetails($user->id);
            
            Mail::to($user->email)->send(new LeaveBalanceInsufficient(
                $user,
                $leaveType,
                0, // No requested days, just a warning
                $balance,
                $computationDetails
            ));

            Log::info('Leave balance warning sent', [
                'user_id' => $user->id,
                'leave_type' => $leaveType->name,
                'balance' => $balance
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send leave balance warning', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send batch leave balance warnings to all users with low balances
     */
    public static function sendLowBalanceWarnings()
    {
        $warningsSent = 0;
        
        try {
            $users = User::where('is_active', true)->get();
            
            foreach ($users as $user) {
                $leaveCredits = $user->leaveCredits()->with('leaveType')->get();
                
                foreach ($leaveCredits as $credit) {
                    // Send warning if balance is 2 days or less
                    if ($credit->balance <= 2 && $credit->balance > 0) {
                        if (self::sendLeaveBalanceWarning($user, $credit->leaveType, $credit->balance)) {
                            $warningsSent++;
                        }
                    }
                }
            }

            Log::info('Low balance warnings sent', [
                'warnings_sent' => $warningsSent
            ]);

            return $warningsSent;
        } catch (\Exception $e) {
            Log::error('Failed to send low balance warnings', [
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Test email configuration
     */
    public static function testEmailConfiguration($testEmail = null)
    {
        try {
            $email = $testEmail ?? config('mail.from.address');
            
            Mail::raw('This is a test email from ISU-Flow Leave Management System.', function($message) use ($email) {
                $message->to($email)
                    ->subject('ISU-Flow Email Test');
            });

            Log::info('Test email sent successfully', ['email' => $email]);
            return true;
        } catch (\Exception $e) {
            Log::error('Test email failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
