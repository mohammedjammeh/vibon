<?php

namespace App\Listeners;

use App\Events\JoinRequestRejected;
use App\Notifications\RequestToJoinVibeRejected;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendJoinRequestRejectedNotification
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
     * @param  JoinRequestRejected  $event
     * @return void
     */
    public function handle(JoinRequestRejected $event)
    {
        $user = $event->joinRequest->user;
        $vibe = $event->joinRequest->vibe;
        $user->notify(new RequestToJoinVibeRejected($vibe->id));
    }
}
