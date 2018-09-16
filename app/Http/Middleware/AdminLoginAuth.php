<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\RedirectResponse;
use Session;

class AdminLoginAuth
{
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
        $this->saveOssSession();

        //session失效
        $adminUser = session('adminUser');
        if (!$adminUser) {
            if ($request->ajax()) {
                return response()->json(
                    array(
                        'code'     => 500,
                        'messages' => array('登录超时,请重新登录'),
                    )
                );
            } else {
                //跳转到登录页
                return new RedirectResponse('/admin');
            }
        }

        return $next($request);
    }

    private function saveOssSession()
    {
        $ossSession = array();

        $ossSession['accessId']      = env('OSS_ACCESS_ID');
        $ossSession['accessKey']     = env('OSS_ACCESS_KEY');
        $ossSession['endpoint']      = env('OSS_ENDPOINT');
        $ossSession['articleBucket'] = env('OSS_FILE_BUCKET');

        session_start();
        $_SESSION['oss'] = $ossSession;
    }
}
