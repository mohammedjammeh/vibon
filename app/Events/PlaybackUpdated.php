<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PlaybackUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $vibeId;
    public $trackId;
    public $isTrackPaused;
    public $type;

    /**
     * Create a new event instance.
     *
     * @param $vibeId
     * @param $trackId
     * @param $isTrackPaused
     * @param $type
     * @return void
     */
    public function __construct($vibeId, $trackId, $isTrackPaused, $type)
    {
        $this->vibeId = $vibeId;
        $this->trackId = $trackId;
        $this->isTrackPaused = $isTrackPaused;
        $this->type = $type;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('playback.updated');
    }

    public function broadcastWith()
    {
        return [
            'vibeId' => $this->vibeId,
            'trackId' => $this->trackId,
            'isTrackPaused' => $this->isTrackPaused,
            'type' => $this->type
        ];
    }
}
