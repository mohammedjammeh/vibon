<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PendingDetachVibeTracksAccepted
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

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('pending_detach_vibe_tracks.accepted');
    }

    public function broadcastWith()
    {
        return [
            'vibe' => $this->pendingVibeTracks->first()->vibe->id
        ];
    }
}
