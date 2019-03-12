<?php

namespace App\Listeners;

use App\AutoDJ\Tracks;
use App\Events\VibeUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateAutoVibeTracks
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
     * @param  VibeUpdated  $event
     * @return void
     */
    public function handle(VibeUpdated $event)
    {
        $autoTracks = app(Tracks::class);
        $autoTracks->updateAPI($event->vibe);
    }
}
