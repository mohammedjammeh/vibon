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
        $vibe->owner->lastUnreadRequestNotificationFor($event->joinRequest)->delete();
    }
}
