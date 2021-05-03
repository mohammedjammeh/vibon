<?php

namespace App\Listeners;

use App\AutoDJ\Tracks as AutoTracks;
use App\Events\AutoVibeRefreshed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RefreshAutoVibeTracks
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
     * @param  AutoVibeRefreshed  $event
     * @return void
     */
    public function handle(AutoVibeRefreshed $event)
    {
        AutoTracks::update($event->vibe);
        AutoTracks::updateAPI($event->vibe);
    }
}
