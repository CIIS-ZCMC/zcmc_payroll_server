<?php

namespace App\Console;

use App\Console\Commands\CheckEmployeeDeductions;
use App\Console\Commands\FetchPayrollData;
use App\Console\Commands\ResumeDeduction;
use App\Console\Commands\SuspendDeductions;
use App\Console\Commands\SuspendReceivables;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command(ResumeDeduction::class)->dailyAt('00:00');
        $schedule->command(CheckEmployeeDeductions::class)->dailyAt('00:00');
        $schedule->command(SuspendDeductions::class)->dailyAt('00:00');
        $schedule->command(SuspendReceivables::class)->dailyAt('00:00');
        $schedule->command(FetchPayrollData::class)->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
