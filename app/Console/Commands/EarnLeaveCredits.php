<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LeaveEarningService;

class EarnLeaveCredits extends Command
{
    protected $signature = 'leave:earn {--month= : Target month in YYYY-MM format}';
    protected $description = 'Automatically earn leave credits for faculty with designation';

    public function handle()
    {
        $month = $this->option('month');
        
        if ($month) {
            try {
                $targetMonth = \Carbon\Carbon::parse($month . '-01');
                $this->info("Processing leave earnings for {$targetMonth->format('F Y')}...");
            } catch (\Exception $e) {
                $this->error("Invalid month format. Use YYYY-MM format.");
                return 1;
            }
        } else {
            $targetMonth = now();
            $this->info("Processing leave earnings for {$targetMonth->format('F Y')}...");
        }
        
        $usersProcessed = LeaveEarningService::autoEarnMonthlyCredits($targetMonth);
        
        if ($usersProcessed > 0) {
            $this->info("✅ Successfully processed leave earnings for {$usersProcessed} users.");
        } else {
            $this->info("ℹ️ No users eligible for leave earnings this month.");
        }
        
        return 0;
    }
}
