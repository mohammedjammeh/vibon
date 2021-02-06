<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TrackVibeStored implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $track;
    public $vibe;

    /**
     * Create a new event instance.
     *
     * @param $track
     * @param $vibe
     * @return void
     */
    public function __construct($track, $vibe)
    {
        $this->track = $track;
        $this->vibe = $vibe;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('track.vibe.stored');
    }

    public function broadcastWith()
    {
        return [
            'vibe' => $this->vibe->id,
            'track' => $this->track->id
        ];
    }
}
