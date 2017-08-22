<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use App\Device;

class DeviceGroupChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $device;

    public $prevGroupId;

    /**
     * Create a old event instance.
     *
     * @return void
     */
    public function __construct(Device $device, $prevGroupId)
    {
        $this->device = $device;
        $this->prevGroupId = $prevGroupId;
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
