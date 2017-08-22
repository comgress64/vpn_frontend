<?php

namespace Vpn\VpnApi\Facades;

use Illuminate\Support\Facades\Facade;

class Vpn extends Facade {
    /**
     * Get the binding in the IoC container
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'vpn'; // the IoC binding.
    }
}

