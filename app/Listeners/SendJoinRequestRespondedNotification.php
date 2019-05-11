<?php

namespace App\Listeners;

use App\Vibe;
use App\Events\JoinRequestResponded;
use App\Notifications\ResponseToJoinAVibe;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendJoinRequestRespondedNotification
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
     * @param  JoinRequestResponded  $event
     * @return void
     */
    public function handle(JoinRequestResponded $event)
    {
        $joinRequest = $event->joinRequest;
        $vibe = Vibe::find($joinRequest->vibe_id);
        $vibe->owner->lastUnreadRequestNotificationFor($joinRequest)->markAsRead();

        if ($vibe->hasMember($joinRequest->user)) {
            $joinRequest->user->notify(new ResponseToJoinAVibe($vibe->id, true));
        } else {
            $joinRequest->user->notify(new ResponseToJoinAVibe($vibe->id, false));
        }
    }
}
