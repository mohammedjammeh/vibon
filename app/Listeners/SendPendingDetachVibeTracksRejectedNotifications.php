<?php

namespace App\Listeners;

use App\Events\PendingDetachVibeTracksRejected;
use App\Notifications\PendingDetachVibeTracksRejectedNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPendingDetachVibeTracksRejectedNotifications
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
     * @param  PendingDetachVibeTracksRejected  $event
     * @return void
     */
    public function handle(PendingDetachVibeTracksRejected $event)
    {
        $event->pendingVibeTracks->each(function ($pendingVibeTrack) {
            $pendingVibeTrack->user->notify(
                new PendingDetachVibeTracksRejectedNotification(
                    $pendingVibeTrack->vibe_id,
                    $pendingVibeTrack->track_id,
                    $pendingVibeTrack->attach
                )
            );
        });
    }
}
