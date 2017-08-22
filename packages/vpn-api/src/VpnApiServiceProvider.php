<?php

namespace Vpn\VpnApi;

use Illuminate\Support\ServiceProvider;

class VpnApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'vpn-api');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {
        $this->app->bind('vpn', function() {
            $url = config('vpn-api.api_url');
            return new \Vpn\VpnApi\Vpn($url);
        });
    }
}
