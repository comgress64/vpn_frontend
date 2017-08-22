<?php

namespace App\Listeners;

use App\Events\DeviceGroupChanged;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OnDeviceGroupChangedUpdateVpn
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
     * @param  DeviceGroupChanged  $event
     * @return void
     */
    public function handle(DeviceGroupChanged $event)
    {
        $device = $event->device;
        $prevGroup = \App\Group::find($event->prevGroupId);
        $newGroup = $device->group;

        if ($newGroup && !$device->isolated) {
            \Vpn::setGroups($device->id, $newGroup->ip . '/112', $newGroup->ports->all(), 'add');
        }

        if ($prevGroup && !$device->isolated) {
            \Vpn::setGroups($device->id, $prevGroup->ip . '/112', $prevGroup->ports->all(), 'remove');
        }
    }
}
