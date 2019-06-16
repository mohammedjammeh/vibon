<?php

namespace App\Listeners;

use App\Vibe;
use App\Events\JoinRequestSent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\RequestToJoinAVibe;

class SendJoinRequestNotification
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
     * @param  JoinRequestSent  $event
     * @return void
     */
    public function handle(JoinRequestSent $event)
    {
        $joinRequest = $event->joinRequest;
        $joinRequest->vibe->owner->notify(new RequestToJoinAVibe($joinRequest->user_id, $joinRequest->vibe->id));
    }
}
