<?php

namespace App\Listeners;

use App\Notifications\JoinedVibe;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendUserJoinedVibeNotification
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
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $event->vibe->owner->notify(new JoinedVibe($event->user->id, $event->vibe->id));
    }
}
