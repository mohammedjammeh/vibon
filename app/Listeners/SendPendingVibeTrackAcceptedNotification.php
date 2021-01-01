<?php

namespace App\Listeners;

use App\Events\PendingVibeTrackAccepted;
use App\Notifications\PendingVibeTrackAcceptedNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPendingVibeTrackAcceptedNotification
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
     * @param  PendingVibeTrackAccepted $event
     * @return void
     */
    public function handle(PendingVibeTrackAccepted $event)
    {
        $event->pendingVibeTrack->user
            ->notify(new PendingVibeTrackAcceptedNotification(
                $event->pendingVibeTrack->vibe->id,
                $event->pendingVibeTrack->track->id)
            );
    }
}
