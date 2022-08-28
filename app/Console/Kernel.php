<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    private static $dailyTasksLock;

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
        if (!static::$dailyTasksLock) {
            $schedule->command('parse:reddit')
                     ->everyFiveMinutes()
                     ->withoutOverlapping(30);
            $schedule->command('parse:score --all')
                     ->dailyAt('3:00')
                     ->withoutOverlapping(30)
                     ->before(fn() => $this->setLock(true));
            $schedule->command('parse:ranks')
                     ->dailyAt('3:00')
                     ->withoutOverlapping(30)
                     ->after(fn() => $this->setLock(false));
        }
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

    private function setLock(bool $lock)
    {
        Kernel::$dailyTasksLock = $lock;
    }
}
