<?php

namespace App\Listeners;

use App\Events\PendingAttachVibeTracksRejected;
use App\Notifications\PendingAttachVibeTrackRejectedNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPendingAttachVibeTracksRejectedNotifications
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
     * @param  PendingAttachVibeTracksRejected $event
     * @return void
     */
    public function handle(PendingAttachVibeTracksRejected $event)
    {
        $event->pendingVibeTracks->each(function ($pendingVibeTrack) {
            $pendingVibeTrack->user->notify(
                new PendingAttachVibeTrackRejectedNotification(
                    $pendingVibeTrack->vibe_id,
                    $pendingVibeTrack->track_id,
                    $pendingVibeTrack->attach
                )
            );
        });
    }
}
