<?php

namespace App\Listeners;

use App\Events\Liked;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendLikedNotification
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
     * @param  Liked  $event
     * @return void
     */
    public function handle(Liked $event)
    {
        $from_user = $event->like->user;
        $user_id = $event->like->likeable->user_id;

        // stop propagation of the event and do nothing
        // if someone likes their likeable
        if ( $from_user->id == $user_id ) {
            return false;
        }

        $message = "$from_user->username liked your ";
        if ( $event->like->likeable_type == 'App\Post' ) {
            $message .= 'post!';
        } else {
            $message .= 'comment!';
        }


        \App\Notification::firstOrCreate([
            'user_id' => $user_id,
            'from_id' => $from_user->id,
            'message' => $message,
            'link' => $event->likeable_link,
        ]);
    }
}
