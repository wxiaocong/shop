<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Session;

class AdminRightAuth
{
    const ACTION_PREFIX = 'App\\Http\\Controllers\\Admins\\';

    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $publicRights = array(
            self::ACTION_PREFIX . 'LoginController@logout',
            self::ACTION_PREFIX . 'HomeController@show',
            self::ACTION_PREFIX . 'System\\AdminUserController@editPassword',
            self::ACTION_PREFIX . 'System\\AdminUserController@updatePassword',
        );

        $action = $request->route()->getActionName();
        //退出无需权限验证
        if (in_array($action, $publicRights)) {
            return $next($request);
        }

        //最终的超级管理员,不走权限中间件
        if (session('adminUser')->id == 1) {
            return $next($request);
        }

        //权限判断
        $adminRightActions = session('adminRightActions');
        if (!in_array($action, $adminRightActions)) {
            if ($request->ajax()) {
                return response()->json(
                    array(
                        'code'     => 401,
                        'messages' => array('您没有相应的权限，请联系管理员赋予相应权限。'),
                    )
                );
            } else {
                abort(401, '您没有相应的权限，请联系管理员赋予相应权限。');
            }
        } else {
            return $next($request);
        }
    }
}
