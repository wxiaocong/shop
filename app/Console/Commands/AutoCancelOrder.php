<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class AutoCancelOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autoCancelOrder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'auto cancel order';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::update('UPDATE `order` SET state = 6, updated_at = ? WHERE state = 1 AND UNIX_TIMESTAMP(created_at) + ? < UNIX_TIMESTAMP(NOW())', [date('Y-m-d H:i:s'), config('system.orderOvertime')]);
    }
}
