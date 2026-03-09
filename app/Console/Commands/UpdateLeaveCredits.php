<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LeaveCreditService;

class UpdateLeaveCredits extends Command
{
    protected $signature = 'leave:credits:update {--user= : User ID to update (optional)}';
    protected $description = 'Update leave credits for all users or specific user';

    public function handle()
    {
        $userId = $this->option('user');
        
        if ($userId) {
            // Update specific user
            $this->info("Updating leave credits for user ID: {$userId}");
            $credits = LeaveCreditService::updateUserAllLeaveCredits($userId);
            
            if ($credits) {
                $this->info("Updated " . count($credits) . " leave credit records for user {$userId}");
            } else {
                $this->error("Failed to update credits for user {$userId}");
            }
        } else {
            // Update all users
            $this->info('Updating leave credits for all users...');
            $updatedUsers = LeaveCreditService::batchUpdateAllUsers();
            
            $this->info("Updated leave credits for " . count($updatedUsers) . " users");
            
            foreach ($updatedUsers as $userData) {
                $user = $userData['user'];
                $credits = $userData['credits'];
                $this->line("  - {$user->full_name}: " . count($credits) . " leave types updated");
            }
        }
        
        $this->info('Leave credits update completed successfully!');
        return 0;
    }
}
