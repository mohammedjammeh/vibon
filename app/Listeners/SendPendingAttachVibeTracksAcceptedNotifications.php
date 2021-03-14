<?php

namespace App\Listeners;

use App\Events\PendingAttachVibeTracksAccepted;
use App\Notifications\PendingAttachVibeTracksAcceptedNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPendingAttachVibeTracksAcceptedNotifications
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
     * @param  PendingAttachVibeTracksAccepted $event
     * @return void
     */
    public function handle(PendingAttachVibeTracksAccepted $event)
    {
        $event->pendingVibeTracks->each(function ($pendingVibeTrack) {
            $pendingVibeTrack->user->notify(
                new PendingAttachVibeTracksAcceptedNotification(
                    $pendingVibeTrack->vibe_id,
                    $pendingVibeTrack->track_id,
                    $pendingVibeTrack->attach
                )
            );
        });
    }
}
