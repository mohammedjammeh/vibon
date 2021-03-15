<?php

namespace App\Listeners;

use App\Events\PendingDetachVibeTracksAccepted;
use App\Events\PendingDetachVibeTracksRejected;
use App\Events\PendingDetachVibeTracksRespondedTo;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyPendingDetachVibeTracksUsers
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
     * @param  PendingDetachVibeTracksRespondedTo  $event
     * @return void
     */
    public function handle(PendingDetachVibeTracksRespondedTo $event)
    {
        $acceptedPendingVibeTracks = $event->pendingVibeTracks->wherein('track_id', $event->responses['accepted']);
        $rejectedPendingVibeTracks = $event->pendingVibeTracks->wherein('track_id', $event->responses['rejected']);


        if($acceptedPendingVibeTracks->isNotEmpty()) {
            broadcast(new PendingDetachVibeTracksAccepted($acceptedPendingVibeTracks))->toOthers();
        }

        if($rejectedPendingVibeTracks->isNotEmpty()) {
            broadcast(new PendingDetachVibeTracksRejected($rejectedPendingVibeTracks))->toOthers();
        }
    }
}
