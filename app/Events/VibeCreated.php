<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class VibeCreated
{
    use Dispatchable, SerializesModels;

    public $vibe;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($vibe)
    {
        $this->vibe = $vibe;
    }
}
