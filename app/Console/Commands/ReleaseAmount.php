<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class ReleaseAmount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'releaseAmount';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Release Amount';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::update('UPDATE `users` SET lockBalance = lockBalance - pre_release_amount, pre_release_amount = 0, updated_at = ? WHERE pre_release_amount > 0', [date('Y-m-d H:i:s')]);
    }
}
