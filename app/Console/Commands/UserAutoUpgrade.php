<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

class AutoCancelOrder extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'userAutoUpgrate';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'user auto upgrate';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		DB::update('UPDATE users u,`order` o SET u.`level` = 1 WHERE u.id = o.user_id AND u.`level` = 0 AND o.state=8 AND DATEDIFF(CURRENT_DATE(),LEFT(o.deliver_time,10)) > ?', [config('system.upgradeDay')]);
	}
}
