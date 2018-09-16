<?php

namespace App\Providers;

use App\Utils\RSA;
use Illuminate\Support\ServiceProvider;

class RSAServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the application services.
     *
     * @return  void
     */
    public function register()
    {
        $this->app->singleton('rsa', function () {
            return new RSA;
        });
    }

    /**
     * Get the services provided by the provider
     *
     * @return  string
     */
    public function provides()
    {
        return array('rsa');
    }
}
