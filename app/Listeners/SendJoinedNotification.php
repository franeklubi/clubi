<?php

namespace App\Listeners;

use App\Events\Joined;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendJoinedNotification
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
     * @param  Joined  $event
     * @return void
     */
    public function handle(Joined $event)
    {
        $from_user = $event->user;
        $user_id = $event->group->owner_id;

        // stop propagation of the event and do nothing
        // if user is the owner of the group
        if ( $from_user->id == $user_id ) {
            return false;
        }

        $group_name = $event->group->name;

        $message = "$from_user->username has joined $group_name!";

        createNotification(
            $user_id,
            $message,
            $from_user->id,
            $event->group_link,
        );
    }
}
