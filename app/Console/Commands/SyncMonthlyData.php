<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MonthlySyncService;
use Carbon\Carbon;

class SyncMonthlyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-monthly-data {--year= : The year to sync (default: current year)} {--month= : The month to sync (default: last month)} {--pending : Check pending DTR submissions only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync monthly DTR data and update leave balances';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $syncService = new MonthlySyncService();
        
        if ($this->option('pending')) {
            $this->info('Checking for pending DTR submissions...');
            $results = $syncService->checkPendingDTRSubmissions();
            
            $this->info("Employees checked: {$results['employees_checked']}");
            $this->info("Reminders sent: {$results['reminders_sent']}");
            $this->info("Total pending days: {$results['pending_days']}");
            
            return Command::SUCCESS;
        }

        $year = $this->option('year') ?: Carbon::now()->year;
        $month = $this->option('month') ?: Carbon::now()->subMonth()->month;

        $this->info("Starting monthly sync for {$year}-{$month}...");
        
        $results = $syncService->syncMonthly($year, $month);
        
        $this->info("Sync completed successfully!");
        $this->info("Employees synced: {$results['synced_employees']}");
        $this->info("DTRs processed: {$results['dtr_processed']}");
        $this->info("Leave balances updated: {$results['leave_balances_updated']}");
        $this->info("CTO hours earned: {$results['cto_earned']}");
        $this->info("Notifications sent: {$results['notifications_sent']}");
        
        if (!empty($results['errors'])) {
            $this->error("Errors encountered:");
            foreach ($results['errors'] as $error) {
                $this->error("  - {$error}");
            }
        }
        
        return Command::SUCCESS;
    }
}
