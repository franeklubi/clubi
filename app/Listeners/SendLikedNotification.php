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

        // attach text content of likeable
        $content = $event->like->likeable->content;
        if ( $content ) {
            $max_length = config('consts.max_notification_message_length');

            $message .= "\n".substr($content, 0, $max_length);

            // if content longer than message length
            strlen($content) > $max_length?
                $message .= '...':false;
        }

        createNotification(
            $user_id,
            $message,
            $from_user->id,
            $event->likeable_link
        );
    }
}
