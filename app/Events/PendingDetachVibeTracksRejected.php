<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PendingDetachVibeTracksRejected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pendingVibeTracks;

    /**
     * Create a new event instance.
     *
     * @param $pendingVibeTracks
     * @return void
     */
    public function __construct($pendingVibeTracks)
    {
        $this->pendingVibeTracks = $pendingVibeTracks;
    }
}
