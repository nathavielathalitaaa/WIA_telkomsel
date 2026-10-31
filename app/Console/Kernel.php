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
        // Define scheduled tasks here if needed.
    }

    /**
     * Register the commands for the application.
     * NOTE: In Laravel 12, commands are registered via withCommands() in bootstrap/app.php.
     * This file is kept for scheduled task definitions only.
     */
    protected function commands(): void
    {
        require base_path('routes/console.php');
    }
}
