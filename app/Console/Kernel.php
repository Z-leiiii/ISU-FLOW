<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\UpdateLeaveCredits::class,
        \App\Console\Commands\SendEmailNotifications::class,
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
            
        // Schedule low balance warnings weekly
        $schedule->command('email:send-notifications low-balance')
            ->weekly()
            ->mondays()
            ->at('09:00')
            ->description('Send low balance warnings to users');
    }
}
