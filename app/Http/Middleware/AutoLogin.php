<?php

namespace App\Http\Middleware;

use Closure;
use EasyWeChat;
use App\Services\Users\UserService;
use App\Services\Users\WechatUserService;

class AutoLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! $request->ajax()) {
            if (empty(session('openid')) && isWeixin()) {
                //微信浏览器获取用户openid
                $app   = EasyWeChat::officialAccount();
                $oauth = $app->oauth;
                session(array('target_url' => url()->full()));
                return $oauth->redirect();
            } elseif (empty(session('user')->id)) {
                //openid已绑定自动登录
                $wechatUserInfo = WechatUserService::findByOpenid(session('openid'));
                if (!empty($wechatUserInfo->user_id)) {
                    session(array('user' => UserService::findById($wechatUserInfo->user_id)));
                }
            }
        }
        
        return $next($request);
    }
}
