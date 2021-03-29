<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PendingDetachVibeTracksRespondedTo implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

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

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('pending_detach_vibe_tracks.responded_to');
    }

    public function broadcastWith()
    {
        return [
            'vibe' => $this->pendingVibeTracks->first()->vibe_id,
            'responses' => $this->responses
        ];
    }
}
