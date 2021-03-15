<?php

namespace App\Listeners;

use App\Events\PendingDetachVibeTracksAccepted;
use App\Notifications\PendingDetachVibeTracksAcceptedNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPendingDetachVibeTracksAcceptedNotifications
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
     * @param  PendingDetachVibeTracksAccepted  $event
     * @return void
     */
    public function handle(PendingDetachVibeTracksAccepted $event)
    {
        $event->pendingVibeTracks->each(function ($pendingVibeTrack) {
            $pendingVibeTrack->user->notify(
                new PendingDetachVibeTracksAcceptedNotification(
                    $pendingVibeTrack->vibe_id,
                    $pendingVibeTrack->track_id,
                    $pendingVibeTrack->attach
                )
            );
        });
    }
}
