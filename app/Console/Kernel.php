<?php

namespace App\Console;
use App\Jobs\DeactivateInactiveRegisteredApplicants;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use App\Jobs\DeactivateUsersWithInactiveApplications;


class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        //    Log::info("Scheduler running....");
            $schedule->command('reset:rgr-status')->cron('0 0 1 1 *'); // At 00:00 on January 1st every year
            // $schedule->command('payment-status-check')->dailyAt('12:00'); // We have commented this because we are using this app:check-pending-payment-status'
            // $schedule->job(new DeactivateInactiveRegisteredApplicants)->dailyAt('12:00');
            // $schedule->job(new DeactivateUsersWithInactiveApplications)->dailyAt('12:00');
              $schedule->job(new DeactivateInactiveRegisteredApplicants)->everyMinute();
            // $schedule->job(new DeactivateUsersWithInactiveApplications)->everyMinute();
            $schedule->command('demand:withdraw')->dailyAt('11:00');
            $schedule->command('app:update-action-taken-by')->dailyAt('11:00');
           $schedule->command('clear:otps')->hourly();
            $schedule->command('app:revert-user-registration-to-section')->dailyAt('11:00');
           $schedule->command('app:check-pending-payment-status')->dailyAt('12:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
