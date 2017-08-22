<?php

namespace App\Listeners;

use App\Events\UserUpdatedGroups;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Group;

class OnUserUpdatedGroupsUpdateVpn implements ShouldQueue
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
     * @param  UserUpdatedGroups  $event
     * @return void
     */
    public function handle(UserUpdatedGroups $event)
    {
        $user = $event->user;

        $user->load('ownDevice');

        Group::whereIn('id', $event->addedIds)->get()->each(function($group) use ($user) {
            if (!$user->ownDevice->isolated) {
                \Vpn::setGroups($user->ownDevice->id, $group->ip . '/112', $group->ports->all(), 'add');
            }
        });

        Group::whereIn('id', $event->removedIds)->get()->each(function($group) use ($user) {
            \Vpn::setGroups($user->ownDevice->id, $group->ip . '/112', $group->ports->all(), 'remove');
        });
    }
}
