<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class VibeDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $vibe;
    public $message;

    /**
     * Create a new event instance.
     *
     * @param $vibe
     * @param $message
     * @return void
     */
    public function __construct($vibe, $message)
    {
        $this->vibe = $vibe;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('vibe.deleted');
    }

    public function broadcastWith()
    {
        return [
            'vibe' => $this->vibe->id,
            'message' => $this->message
        ];
    }
}
