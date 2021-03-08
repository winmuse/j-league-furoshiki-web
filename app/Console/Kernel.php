<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        // production
        $schedule->command('fetch:aws')->everyFifteenMinutes()->runInBackground();
        $schedule->command('delete:aws 30')->daily()->runInBackground();
        $schedule->command('fetch:dropbox')->everyFifteenMinutes()->runInBackground();
        $schedule->command('fetch:dropbox:jleague')->everyFifteenMinutes()->runInBackground();
        $schedule->command('delete:dropbox 30')->daily()->runInBackground();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
