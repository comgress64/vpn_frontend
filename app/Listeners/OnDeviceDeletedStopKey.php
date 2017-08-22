<?php

namespace App\Listeners;

use App\Events\DeviceDeleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OnDeviceDeletedStopKey
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
     * @param  DeviceDeleted  $event
     * @return void
     */
    public function handle(DeviceDeleted $event)
    {
        $device = $event->device;

        \Vpn::stopKey($device->id, 'remove');
    }
}
