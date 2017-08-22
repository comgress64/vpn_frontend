<?php

namespace App\Listeners;

use App\Events\GroupDeleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OnGroupDeletedUpdateVpn
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
     * @param  GroupDeleted  $event
     * @return void
     */
    public function handle(GroupDeleted $event)
    {
        $group = $event->group;

        $group->devices->each(function($device) use ($group) {
            \Vpn::setGroups($device->id, $group->ip . '/112', $group->ports->all(), 'remove');
            \Vpn::stopKey($device->id, 'remove');
        });
    }
}
