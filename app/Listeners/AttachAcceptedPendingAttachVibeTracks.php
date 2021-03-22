<?php

namespace App\Listeners;

use App\Events\PendingAttachVibeTracksRespondedTo;
use App\MusicAPI\Playlist;
use App\Track;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AttachAcceptedPendingAttachVibeTracks
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
        $vibe = $event->pendingVibeTracks->first()->vibe;

        if (!$vibe->auto_dj) {
            $playlist = app(Playlist::class)->get($vibe->api_id);
            $playlistTracksIDs = collect($playlist->tracks->items)->pluck('track.id');
            $tracks = Track::whereIn('id', $event->responses['accepted'])->whereNotIn('api_id', $playlistTracksIDs);

            app(Playlist::class)->addTracks($vibe, $tracks->pluck('api_id')->toArray());
        }
    }
}
