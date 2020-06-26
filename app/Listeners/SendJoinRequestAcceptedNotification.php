<?php

namespace App\Listeners;

use App\Events\JoinRequestAccepted;
use App\Notifications\ResponseToJoinAVibe;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendJoinRequestAcceptedNotification
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
     * @param  JoinRequestAccepted  $event
     * @return void
     */
    public function handle(JoinRequestAccepted $event)
    {
        $user = $event->joinRequest->user;
        $vibe = $event->joinRequest->vibe;
        $user->notify(new ResponseToJoinAVibe($vibe->id, true));
    }
}
