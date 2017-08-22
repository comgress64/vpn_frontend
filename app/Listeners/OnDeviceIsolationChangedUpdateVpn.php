<?php

namespace App\Listeners;

use App\Events\DeviceIsolationChanged;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OnDeviceIsolationChangedUpdateVpn
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  DeviceIsolationChanged  $event
     * @return void
     */
    public function handle(DeviceIsolationChanged $event)
    {
        $device = $event->device;

        if ($device->isolated) {
            if (!$device->group && $device->user && $device->user->isSuperadmin()) {
                \Vpn::setGroups($device->id, env('VPN_GLOBAL_GROUP') . '/48', [], 'remove');
                return;
            }

            \Vpn::setGroups($device->id, $device->group->ip . '/112', [], 'remove');
            if ($device->user) {
                $device->user->groups->each(function($group) use ($device) {
                    \Vpn::setGroups($device->id, $group->ip . '/112', $group->ports->all(), 'remove');
                });
            }
        }
        else {
            if (!$device->group && $device->user && $device->user->isSuperadmin()) {
                \Vpn::setGroups($device->id, env('VPN_GLOBAL_GROUP') . '/48', [], 'add');
                return;
            }

            \Vpn::setGroups($device->id, $device->group->ip . '/112', [], 'add');
            if ($device->user) {
                $device->user->groups->each(function($group) use ($device) {
                    \Vpn::setGroups($device->id, $group->ip . '/112', $group->ports->all(), 'add');
                });
            }
        }

    }
}
