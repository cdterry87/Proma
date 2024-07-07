<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(new \App\Jobs\RoutineCleanupJob())->dailyAt('00:00');

        $schedule->job(new \App\Jobs\IssuesNotificationsJob())->dailyAt('02:00');
        $schedule->job(new \App\Jobs\ProjectsNotificationsJob())->dailyAt('04:00');
        $schedule->job(new \App\Jobs\ProjectsTasksNotificationsJob())->dailyAt('06:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
