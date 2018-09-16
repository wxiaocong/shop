<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Session;

class UserAuth
{
    const ACTION_PREFIX = 'App\\Http\\Controllers\\Users\\';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //session失效，跳转到登录页
        $user = session('user');
        if (!$user) {
            return new RedirectResponse('/login');
        }

        return $next($request);
    }
}
