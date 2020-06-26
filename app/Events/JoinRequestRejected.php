<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class JoinRequestRejected implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $joinRequest;

    /**
     * Create a new event instance.
     *
     * @param $joinRequest
     * @return void
     */
    public function __construct($joinRequest)
    {
        $this->joinRequest = $joinRequest;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('join.request.rejected');
    }

    public function broadcastWith()
    {
        return [
            'vibe' => $this->joinRequest->vibe->id
        ];
    }
}
