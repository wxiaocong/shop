<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AgentType;
use DB;

class AutoReturnEnsureMoney extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AutoReturnEnsureMoney';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'auto return ensure money';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    }
}
