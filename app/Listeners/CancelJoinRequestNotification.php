<?php

namespace App\Listeners;

use App\Vibe;
use App\Events\JoinRequestCancelled;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CancelJoinRequestNotification
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
     * @param  JoinRequestCancelled  $event
     * @return void
     */
    public function handle(JoinRequestCancelled $event)
    {
        $vibe = Vibe::find($event->joinRequest->vibe_id);

//         this should be changed to just delete the notification for the joinRequest.. Shouldn't depend on read or not
//        makes sense because depending on ui/ux, the notification might have already been read and not ignored
//        $vibe->owner->lastUnreadRequestNotificationFor($event->joinRequest)->delete();

        $vibe->owner->lastJoinRequestNotificationFor($event->joinRequest)->delete();
    }
}
