<?php

namespace App\Listeners;

use App\Notifications\LeftVibe;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendUserLeftVibeNotification
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
        $event->vibe->owner->notify(new LeftVibe($event->user->id, $event->vibe->id));
    }
}
