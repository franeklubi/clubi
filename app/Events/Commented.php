<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Commented
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $comment;
    public $post_link;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(\App\Comment $comment)
    {
        $this->comment = $comment;
        $this->post_link = $comment->link();
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
