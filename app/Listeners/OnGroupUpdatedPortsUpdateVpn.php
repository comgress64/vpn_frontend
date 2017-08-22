<?php

namespace App\Listeners;

use App\Events\GroupUpdatedPorts;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OnGroupUpdatedPortsUpdateVpn
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
     * @param  GroupUpdatedPorts  $event
     * @return void
     */
    public function handle(GroupUpdatedPorts $event)
    {
        $group = $event->group;
        $addedPorts = $event->addedPorts;
        $removedPorts = $event->removedPorts;

        $group->users->each(function($user) use ($group, $addedPorts, $removedPorts) {
            $device = $user->ownDevice;
            if ($device && !$device->isolated) {
                $this->updateDevice($device, $group, $addedPorts, $removedPorts);
            }
        });

        $group->devices->each(function($device) use ($group, $addedPorts, $removedPorts) {
            if (!$device->isolated) {
                $this->updateDevice($device, $group, $addedPorts, $removedPorts);
            }
        });
    }

    protected function updateDevice($device, $group, $addedPorts, $removedPorts) {
        if (!empty($addedPorts)) {
            \Vpn::setGroups($device->id, $group->ip . '/112', $addedPorts, 'add');
        }

        if (!empty($removedPorts)) {
            \Vpn::setGroups($device->id, $group->ip . '/112', $removedPorts, 'remove');
        }
    }
}
