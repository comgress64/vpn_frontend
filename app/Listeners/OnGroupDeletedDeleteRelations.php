<?php

namespace App\Listeners;

use App\Events\GroupDeleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OnGroupDeletedDeleteRelations
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

        $group->devices->each(function($device) {
            $device->delete();
        });

        $group->groupPorts()->delete();

        // Delete those users, that were only attached to current group
        $group->users()->whereDoesntHave('groups', function($query) use ($group) {
            $query->where('groups.id', '!=', $group->id);
        })->delete();

        // Remove all connections
        \DB::table('admin_user_groups')->whereGroupId($group->id)->delete();
    }
}
