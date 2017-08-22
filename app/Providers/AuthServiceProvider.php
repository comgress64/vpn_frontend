<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Group' => 'App\Policies\GroupPolicy',
        'App\Device' => 'App\Policies\DevicePolicy',
        'App\User' => 'App\Policies\UserPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('users-list', function ($user) {
            return !$user->isUser();
        });

        Gate::define('groups-list', function ($user) {
            return $user->isSuperadmin() || $user->hasPermission('manage_groups');
        });

        Gate::define('devices-list', function ($user) {
            return $user->isSuperadmin() || $user->hasPermission('manage_devices');
        });

        Gate::define('get-device-vpn-key', function ($user) {
            return $user->isSuperadmin() || $user->hasPermission('manage_devices');
        });

        Gate::define('get-user-vpn-key', function ($user, $keyUser) {
            return $user->isSuperadmin() || $user == $keyUser;
        });

    }
}
