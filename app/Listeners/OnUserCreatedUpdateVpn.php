<?php

namespace App\Listeners;

use App\Events\UserCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OnUserCreatedUpdateVpn implements ShouldQueue
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
     * @param  UserCreated  $event
     * @return void
     */
    public function handle(UserCreated $event)
    {
        $user = $event->user;

        $groupId = $user->isSuperadmin()
            ? null
            : $user->groups()->orderBy('id', 'ASC')->first()->id;

        // Create main device for new user
        $device = \App\Device::create([
            'group_id' => $groupId,
            'user_id' => $user->id,
            'creator_id' => $user->id,
            'isolated' => false,
        ]);

        // Attach superadmin to global group that has access to all networks
        if ($user->isSuperadmin()) {
            \Vpn::setGroups($device->id, env('VPN_GLOBAL_GROUP') . '/48');
        }
        else {
            $group = $user->groups()->where('groups.id', $groupId)->first();

            \Vpn::setGroups($device->id, $group->ip . '/112', []);

            $otherGroupIds = $user->groups()->where('groups.id', '!=', $groupId)->pluck('groups.id')->all();

            // Fire event for user's new attached groups except first to send them to vpn api
            event(new \App\Events\UserUpdatedGroups($user, $otherGroupIds, []));
        }
    }
}
