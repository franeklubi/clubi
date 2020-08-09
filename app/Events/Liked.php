<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Liked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $like;   // Like instance
    public $likeable_link;  // link to the resource

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(\App\Like $like)
    {
        $this->like = $like;
        $this->likeable_link = $like->likeable->link();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
