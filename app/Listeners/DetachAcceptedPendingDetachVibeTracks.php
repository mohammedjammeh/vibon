<?php

namespace App\Listeners;

use App\Events\PendingDetachVibeTracksRespondedTo;
use App\MusicAPI\Playlist;
use App\Track;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DetachAcceptedPendingDetachVibeTracks
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
        $vibe = $event->pendingVibeTracks->first()->vibe;

        if ($vibe->auto_dj) {
            return;
        }

        $tracks = Track::whereIn('id', $event->responses['accepted']);
        app(Playlist::class)->deleteTracks($vibe, $tracks->pluck('api_id')->toArray());

    }
}
