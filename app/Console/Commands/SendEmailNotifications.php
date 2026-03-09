<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailNotificationService;

class SendEmailNotifications extends Command
{
    protected $signature = 'email:send-notifications {type=low-balance : Type of notifications to send (low-balance, test)} {--email= : Email address for test notifications}';
    protected $description = 'Send email notifications for leave balance warnings and testing';

    public function handle()
    {
        $type = $this->argument('type');
        $email = $this->option('email');
        
        switch ($type) {
            case 'low-balance':
                $this->info('Sending low balance warnings...');
                $sent = EmailNotificationService::sendLowBalanceWarnings();
                $this->info("Sent {$sent} low balance warnings.");
                break;
                
            case 'test':
                $this->info('Sending test email...');
                $success = EmailNotificationService::testEmailConfiguration($email);
                if ($success) {
                    $this->info('Test email sent successfully!');
                } else {
                    $this->error('Failed to send test email.');
                }
                break;
                
            default:
                $this->error('Invalid notification type. Use: low-balance or test');
                return 1;
        }
        
        return 0;
    }
}
