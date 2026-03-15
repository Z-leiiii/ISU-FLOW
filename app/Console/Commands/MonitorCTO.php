<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CTOMonitoringService;

class MonitorCTO extends Command
{
    protected $signature = 'cto:monitor {--days=30 : Days threshold for expiration alerts} {--process-expired : Process expired CTO records}';
    protected $description = 'Monitor CTO expiration and send alerts';

    public function handle()
    {
        $daysThreshold = $this->option('days');
        $processExpired = $this->option('process-expired');
        
        $this->info("🔍 Monitoring CTO with {$daysThreshold} days threshold...");
        
        // Check for expiring CTO
        $alertsSent = CTOMonitoringService::checkExpiringCTO($daysThreshold);
        $this->info("📧 Sent {$alertsSent} expiration alerts.");
        
        // Process expired CTO if requested
        if ($processExpired) {
            $this->info("🗑️ Processing expired CTO records...");
            $expiredCount = CTOMonitoringService::processExpiredCTO();
            $this->info("📊 Processed {$expiredCount} expired CTO records.");
        }
        
        // Show summary
        $expiringSoon = CTOMonitoringService::getExpiringSoonForAll($daysThreshold);
        if ($expiringSoon->count() > 0) {
            $this->info("\n📋 Expiring Soon Summary:");
            $this->table(
                ['User', 'Hours', 'Expires', 'Days Left'],
                $expiringSoon->map(function($cto) {
                    return [
                        $cto['user_name'],
                        $cto['remaining_hours'],
                        $cto['expires_at'],
                        $cto['days_until_expiration']
                    ];
                })
            );
        } else {
            $this->info("✅ No CTO records expiring in the next {$daysThreshold} days.");
        }
        
        return 0;
    }
}
