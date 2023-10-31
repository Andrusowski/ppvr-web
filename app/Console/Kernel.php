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
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('parse:reddit')
                 ->everyFiveMinutes()
                 ->withoutOverlapping(5);
        $schedule->command('parse:ranks')
                 ->dailyAt('3:00')
                 ->withoutOverlapping(1);
        $schedule->command('parse:player:score')
                 ->dailyAt('3:10')
                 ->withoutOverlapping(30);
        $schedule->command('parse:author:score')
                 ->dailyAt('3:40')
                 ->withoutOverlapping(30);
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
