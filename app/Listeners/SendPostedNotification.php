<?php

namespace App\Listeners;

use App\Events\Posted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPostedNotification
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
     * @param  Posted  $event
     * @return void
     */
    public function handle(Posted $event)
    {
        $group = $event->post->group;
        $from_user = $event->post->user;
        $user_id = $group->owner_id;

        // stop propagation of the event and do nothing
        // if user is the owner of the group
        if ( $from_user->id == $user_id ) {
            return false;
        }

        $message = "$from_user->username has posted in $group->name!";

        // attach text content of the post
        $content = $event->post->content;
        if ( $content ) {
            $max_length = config('consts.max_notification_message_length');

            $message .= "\n\"".substr($content, 0, $max_length);

            // if content longer than message length
            strlen($content) > $max_length?
                $message .= '...':false;

            $message .= "\"";
        }

        createNotification(
            $user_id,
            $message,
            $from_user->id,
            $event->post_link,
        );
    }
}
