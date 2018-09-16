<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = array(
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
    );

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = array(
        'web' => array(
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
        ),

        'api' => array(
            'throttle:60,1',
        ),
    );

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = array(
        'auth'            => \App\Http\Middleware\Authenticate::class,
        'auth.basic'      => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'guest'           => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle'        => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'userAuth'        => \App\Http\Middleware\UserAuth::class,
        'adminLoginAuth'  => \App\Http\Middleware\AdminLoginAuth::class,
        'adminRightAuth'  => \App\Http\Middleware\AdminRightAuth::class,
        'adminMenuSelect' => \App\Http\Middleware\AdminMenuSelect::class,
        'routeHistory'    => \App\Http\Middleware\RouteHistory::class,
        'autoLogin'       => \App\Http\Middleware\AutoLogin::class,
    );
}
