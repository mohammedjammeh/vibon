<?php

namespace Tests\Unit\Controllers;

use App\Events\TrackVibeDestroyed;
use App\Events\TrackVibeStored;
use App\Track;
use App\User;
use App\Vibe;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrackVibeTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_that_the_track_vibe_stored_event_is_called_when_a_track_is_added_to_a_vibe()
    {
        Event::fake();

        $track = factory(Track::class)->create();
        $vibe = factory(Vibe::class)->create();
        $user = factory(User::class)->create();
        $vibe->users()->attach($user->id, ['owner' => true]);

        $this->post(route('track-vibe.store', [$vibe, $track->api_id]));

        Event::assertDispatched(TrackVibeStored::class);
    }

    public function test_that_the_track_vibe_destroyed_event_is_called_when_a_track_is_removed_from_a_vibe()
    {
        Event::fake();

        $vibe = factory(Vibe::class)->create();
        $track = factory(Track::class)->create();
        $user = factory(User::class)->create();
        $vibe->tracks()->attach($track->id, ['auto_related' => false]);
        $vibe->users()->attach($user->id, ['owner' => true]);

        $this->delete(route('track-vibe.destroy', [
            'vibe' => $vibe->id,
            'track' => $track->id
        ]));

        Event::assertDispatched(TrackVibeDestroyed::class);
    }
}
