<?php

namespace App\Listeners;

use App\Events\Commented;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendCommentedNotification
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
     * @param  Commented  $event
     * @return void
     */
    public function handle(Commented $event)
    {
        $from_user = $event->comment->user;
        $user_id = $event->comment->post->user_id;

        // stop propagation of the event and do nothing
        // if someone comments on their post
        if ( $from_user->id == $user_id ) {
            return false;
        }

        $message = "$from_user->username commented on your post! ";

        // attach text content of the comment
        $content = $event->comment->content;
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
