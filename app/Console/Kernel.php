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
        'App\Console\Commands\PreReleaseAmount',
        'App\Console\Commands\ReleaseAmount',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) {
        $schedule->command('autoCancelOrder')->everyFiveMinutes();//自动取消超时订单
        $schedule->command('preReleaseAmount')->monthlyOn(1, '0:00');;//每月1号计算锁定金额
        $schedule->command('preReleaseAmount')->monthlyOn(7, '0:00');;//每月7号解除上月锁定金额
    }
}
