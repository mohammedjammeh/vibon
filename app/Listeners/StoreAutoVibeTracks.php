<?php

namespace App\Listeners;

use App\AutoDJ\Tracks;
use App\Events\VibeCreated; 
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class StoreAutoVibeTracks
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
     * @param  VibeCreated  $event
     * @return void
     */
    public function handle(VibeCreated $event)
    {
        $autoTracks = app(Tracks::class);
        $autoTracks->store($event->vibe);
        
        if ($event->vibe->auto_dj) {
            $autoTracks->storeAPI($event->vibe);
        }
    }
}