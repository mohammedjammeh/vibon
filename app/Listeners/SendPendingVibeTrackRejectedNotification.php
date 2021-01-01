<?php

namespace App\Listeners;

use App\Events\PendingVibeTrackRejected;
use App\Notifications\PendingVibeTrackRejectedNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPendingVibeTrackRejectedNotification
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
     * @param  PendingVibeTrackRejected $event
     * @return void
     */
    public function handle(PendingVibeTrackRejected $event)
    {
        $event->pendingVibeTrack->user
            ->notify(new PendingVibeTrackRejectedNotification(
                    $event->pendingVibeTrack->vibe->id,
                    $event->pendingVibeTrack->track->id)
            );
    }
}
