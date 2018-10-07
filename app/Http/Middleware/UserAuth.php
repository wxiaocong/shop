<?php

namespace App\Http\Middleware;

use App\Services\Users\UserService;
use Closure;
use EasyWeChat;

class UserAuth {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if (!isWeixin()) {
            session(array('user' => UserService::findByOpenid(6))); //测试
        } else {
            if (!empty(session('user'))) {
                $userInfo = UserService::findById(session('user')->id);
            }
            if (empty(session('user')) || empty($userInfo)) {
                $app = EasyWeChat::officialAccount();
                $oauth = $app->oauth;
                session(array('target_url' => url()->full()));
                return $oauth->redirect();
            }
        }
        return $next($request);
    }
}
