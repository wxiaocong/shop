<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * RSA facade.
 * The class of RSA can be called by RSA::encrypt() and so on.
 *
 *  @author Benjamin Cao(caojianghui@carnetmotor.com)
 */
class RSA extends Facade
{
    /**
     * Return the service provider.
     *
     * @return  string
     */
    protected static function getFacadeAccessor()
    {
        return 'rsa';
    }
}
