<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\UpdateLeaveCredits::class,
        \App\Console\Commands\SendEmailNotifications::class,
        \App\Console\Commands\EarnLeaveCredits::class,
        \App\Console\Commands\MonitorCTO::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Schedule leave credits update monthly
        $schedule->command('leave:credits:update')
            ->monthly()
            ->when(function () {
                return date('d') === '01'; // Run on 1st of each month
            })
            ->description('Update leave credits for all users');
            
        // Schedule automatic leave earnings monthly
        $schedule->command('leave:earn')
            ->monthly()
            ->on(1) // 1st day of month
            ->at('02:00') // 2 AM
            ->description('Automatically earn leave credits for faculty with designation');
            
        // Schedule low balance warnings weekly
        $schedule->command('email:send-notifications low-balance')
            ->weekly()
            ->mondays()
            ->at('09:00')
            ->description('Send low balance warnings to users');
            
        // Schedule CTO expiration checks
        $schedule->command('cto:monitor --days=30')
            ->daily()
            ->at('09:00')
            ->description('Check CTO expiring in 30 days');
            
        $schedule->command('cto:monitor --days=7')
            ->daily()
            ->at('09:00')
            ->when(function () {
                return date('N') <= 5; // Weekdays only
            })
            ->description('Check CTO expiring in 7 days (weekdays only)');
            
        // Schedule expired CTO processing
        $schedule->command('cto:monitor --process-expired')
            ->daily()
            ->at('01:00')
            ->description('Process expired CTO records');
    }
}
