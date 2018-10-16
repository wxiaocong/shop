<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class PreReleaseAmount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'preReleaseAmount';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pre Release Amount';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::update('UPDATE `users` SET pre_release_amount = lockBalance, updated_at = ? WHERE lockBalance > 0', [date('Y-m-d H:i:s')]);
    }
}
