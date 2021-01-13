<?php

namespace Tests\Unit\Controllers;

use App\Events\TrackVibeDestroyed;
use App\Events\TrackVibeStored;
use App\Track;
use App\User;
use App\Vibe;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrackVibeTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_that_a_vibe_track_can_be_directly_stored_by_an_owner()
    {
        $track = factory(Track::class)->create();
        $vibe = factory(Vibe::class)->create();
        $vibe->users()->attach($this->user->id, ['owner' => true]);

        $this->post(route('track-vibe.store', [$vibe, $track->api_id]));

        $this->assertDatabaseHas('track_vibe', [
            'track_id' => $track->id,
            'vibe_id' => $vibe->id,
            'user_id' => $vibe->owner->id,
            'auto_related' => false
        ]);
    }

    public function test_that_a_vibe_track_cannot_be_directly_stored_by_a_member()
    {
        $track = factory(Track::class)->create();
        $vibe = factory(Vibe::class)->create();
        $vibe->users()->attach($this->user->id, ['owner' => false]);

        $response = $this->post(route('track-vibe.store', [$vibe, $track->api_id]));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('track_vibe', [
            'track_id' => $track->id,
            'vibe_id' => $vibe->id,
            'user_id' => $this->user->id,
            'auto_related' => false
        ]);
    }

    public function test_that_the_track_vibe_stored_event_is_called_when_a_track_is_added_to_a_vibe()
    {
        Event::fake();

        $track = factory(Track::class)->create();
        $vibe = factory(Vibe::class)->create();
        $vibe->users()->attach($this->user->id, ['owner' => true]);

        $this->post(route('track-vibe.store', [$vibe, $track->api_id]));

        Event::assertDispatched(TrackVibeStored::class);
    }

    public function test_that_a_vibe_track_can_be_directly_destroyed_by_an_owner()
    {
        $vibe = factory(Vibe::class)->create();
        $track = factory(Track::class)->create();
        $user = factory(User::class)->create();

        $vibe->users()->attach($this->user->id, ['owner' => true]);
        $vibe->tracks()->attach($track->id, [
            'user_id' => $user->id,
            'auto_related' => false
        ]);

        $this->delete(route('track-vibe.destroy', [
            'vibe' => $vibe->id,
            'track' => $track->id
        ]));

        $this->assertDatabaseMissing('track_vibe', [
            'track_id' => $track->id,
            'vibe_id' => $vibe->id,
            'user_id' => $user->id,
            'auto_related' => false
        ]);
    }

    public function test_that_a_vibe_track_cannot_be_directly_destroyed_by_a_member()
    {
        $vibe = factory(Vibe::class)->create();
        $track = factory(Track::class)->create();
        $user = factory(User::class)->create();

        $vibe->users()->attach($this->user->id, ['owner' => false]);
        $vibe->tracks()->attach($track->id, [
            'user_id' => $user->id,
            'auto_related' => false
        ]);

        $response = $this->delete(route('track-vibe.destroy', [
            'vibe' => $vibe->id,
            'track' => $track->id
        ]));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('track_vibe', [
            'track_id' => $track->id,
            'vibe_id' => $vibe->id,
            'user_id' => $user->id,
            'auto_related' => false
        ]);
    }

    public function test_that_the_track_vibe_destroyed_event_is_called_when_a_track_is_removed_from_a_vibe()
    {
        Event::fake();

        $vibe = factory(Vibe::class)->create();
        $track = factory(Track::class)->create();
        $vibe->tracks()->attach($track->id, ['auto_related' => false]);
        $vibe->users()->attach($this->user->id, ['owner' => true]);

        $this->delete(route('track-vibe.destroy', [
            'vibe' => $vibe->id,
            'track' => $track->id
        ]));

        Event::assertDispatched(TrackVibeDestroyed::class);
    }
}
