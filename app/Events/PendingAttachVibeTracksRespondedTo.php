<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PendingAttachVibeTracksRespondedTo
{
    use Dispatchable, SerializesModels;

    public $pendingVibeTracks;
    public $responses;

    /**
     * Create a new event instance.
     *
     * @param $pendingVibeTracks
     * @param $responses
     * @return void
     */
    public function __construct($pendingVibeTracks, $responses)
    {
        $this->pendingVibeTracks = $pendingVibeTracks;
        $this->responses = $responses;
    }
}
