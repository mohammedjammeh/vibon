<?php

namespace App\Listeners;

use App\Events\PendingAttachVibeTracksAccepted;
use App\Events\PendingAttachVibeTracksRejected;
use App\Events\PendingAttachVibeTracksRespondedTo;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyPendingAttachVibeTracksUsers
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
     * @param  PendingAttachVibeTracksRespondedTo  $event
     * @return void
     */
    public function handle(PendingAttachVibeTracksRespondedTo $event)
    {
        $acceptedPendingVibeTracks = $event->pendingVibeTracks->wherein('track_id', $event->responses['accepted']);
        $rejectedPendingVibeTracks = $event->pendingVibeTracks->wherein('track_id', $event->responses['rejected']);

        if($acceptedPendingVibeTracks->isNotEmpty()) {
            broadcast(new PendingAttachVibeTracksAccepted($acceptedPendingVibeTracks))->toOthers();
        }

        if($rejectedPendingVibeTracks->isNotEmpty()) {
            broadcast(new PendingAttachVibeTracksRejected($rejectedPendingVibeTracks))->toOthers();
        }
    }
}
