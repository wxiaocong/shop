<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {
    /**
     * The Artisan commands provided by your application.
     * * * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\AutoCancelOrder',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) {
        $schedule->command('autoCancelOrder')->everyFiveMinutes();
    }
}
