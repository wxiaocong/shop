<?php

namespace App\Http\Middleware;

use Closure;
use EasyWeChat;
use App\Services\Users\UserService;

class UserAuth {
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next) {
		session(array('user' => UserService::findByOpenid(1)));
		if (empty(session('user'))) {
			$app = EasyWeChat::officialAccount();
			$oauth = $app->oauth;
			session(array('target_url' => url()->full()));
			return $oauth->redirect();
		}
		return $next($request);
	}
}
