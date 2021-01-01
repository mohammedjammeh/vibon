<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PendingVibeTrackCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pendingVibeTrack;

    /**
     * Create a new event instance.
     *
     * @param $pendingVibeTrack
     * @return void
     */
    public function __construct($pendingVibeTrack)
    {
        $this->pendingVibeTrack = $pendingVibeTrack;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
//        return new PrivateChannel('channel-name');
        return new Channel('pending_vibe_track.created');
    }

    public function broadcastWith()
    {
        return [
            'vibe' => $this->pendingVibeTrack->vibe->id
        ];
    }
}
