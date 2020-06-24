<?php

namespace Tests\Unit\Controllers;

use App\Events\TrackVotedDown;
use App\Events\TrackVotedUp;
use App\Track;
use App\User;
use App\Vibe;
use App\Vote;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VoteTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_that_the_track_voted_up_event_gets_called_when_a_track_is_voted_up()
    {
        Event::fake();

        $track = factory(Track::class)->create();
        $vibe = factory(Vibe::class)->create();
        $vibeOwner = factory(User::class)->create();
        $vibe->users()->attach($vibeOwner->id, ['owner' => true]);

        $this->post(route('vote.store', ['vibe' => $vibe, 'track' => $track]));

        Event::assertDispatched(TrackVotedUp::class);
    }

    public function test_that_the_track_voted_down_event_gets_called_when_a_track_is_voted_down()
    {
        Event::fake();

        $track = factory(Track::class)->create();
        $vibe = factory(Vibe::class)->create();
        $vibeOwner = factory(User::class)->create();
        $vibe->users()->attach($vibeOwner->id, ['owner' => true]);

        factory(Vote::class)->create([
            'track_id' => $track->id,
            'vibe_id' => $vibe->id,
            'user_id' => $this->user->id
        ]);

        $this->delete(route('vote.destroy', ['vibe' => $vibe, 'track' => $track]));

        Event::assertDispatched(TrackVotedDown::class);
    }

}
