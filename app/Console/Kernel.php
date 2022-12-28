<?php

namespace App\Console;

use App\Jobs\ScrapePostsFromSearchTitle;
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
        $schedule->call(fn () => (new ScrapePostsFromSearchTitle)->handle())
            ->dailyAt('23:00')
            ->when(fn () => (bool) cache('BAHARUNE'));

        $schedule->command('queue:work', ['--stop-when-empty', '--rest=3', '--timeout=120'])
            ->dailyAt('23:30')
            ->when(fn () => (bool) cache('BAHARUNE'));
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
