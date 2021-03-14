<?php

namespace App\Listeners;

use App\Events\PendingAttachVibeTracksRespondedTo;
use App\PendingVibeTrack;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeletePendingAttachVibeTracks
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
        $responses = array_merge($event->responses['accepted'], $event->responses['rejected']);
        $pendingVibeTracksIDs = $event->pendingVibeTracks->pluck('id')->toArray();

        PendingVibeTrack::whereIn('id', $pendingVibeTracksIDs)
            ->wherein('track_id', $responses)
            ->delete();
    }
}
