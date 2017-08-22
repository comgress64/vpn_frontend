<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use App\Group;

class GroupUpdatedPorts
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $group;

    public $addedPorts;

    public $removedPorts;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Group $group, $addedPorts, $removedPorts)
    {
        $this->group = $group;
        $this->addedPorts = $addedPorts;
        $this->removedPorts = $removedPorts;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        // return new PrivateChannel('channel-name');
    }
}
