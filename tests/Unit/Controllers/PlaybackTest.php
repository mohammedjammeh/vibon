<?php

namespace Tests\Unit\Controllers;

use App\Events\PlaybackUpdated;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlaybackTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_the_broadcast_method_broadcasts_play_back_data()
    {
        Event::fake();
        $attributes = [
            'vibe_id' => $this->faker->slug,
            'track_id' => $this->faker->uuid,
            'is_track_paused' => $this->faker->boolean
        ];

        $this->post(route('playback.broadcast.vibe.track'), $attributes);

        Event::assertDispatched(PlaybackUpdated::class, function ($e) use ($attributes) {
            return
                $e->vibeId === $attributes['vibe_id'] &&
                $e->trackId === $attributes['track_id'] &&
                $e->isTrackPaused === $attributes['is_track_paused'];
        });
    }
}
