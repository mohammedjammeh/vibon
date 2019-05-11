<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class JoinRequestCancelled
{
    use Dispatchable, SerializesModels;

    public $joinRequest;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($joinRequest)
    {
        $this->joinRequest = $joinRequest;
    }
}
