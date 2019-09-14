<?php

namespace Tests\Feature\Controllers;

use App\Track;
use App\Vibe;
use App\Vote;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VoteTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_the_store_method_stores_a_vote_for_a_vibes_track()
    {
        $track = factory(Track::class)->create();
        $vibe = factory(Vibe::class)->create();

        $this->post(route('vote.store', ['vibe' => $vibe, 'track' => $track]));
        $this->assertDatabaseHas('votes', [
            'vibe_id' => $vibe->id,
            'track_id' => $track->id,
            'user_id' => $this->user->id
        ]);
    }

    public function test_the_destroy_method_deletes_a_vote_which_belongs_to_vibes_track()
    {
        $track = factory(Track::class)->create();
        $vibe = factory(Vibe::class)->create();

        $vote = factory(Vote::class)->create([
            'track_id' => $track->id,
            'vibe_id' => $vibe->id,
            'user_id' => $this->user->id
        ]);

        $this->delete(route('vote.destroy', ['vibe' => $vibe, 'track' => $track]));
        $this->assertDatabaseMissing('votes', [
            'id' => $vote->id,
            'vibe_id' => $vibe->id,
            'track_id' => $track->id,
            'user_id' => $this->user->id
        ]);
    }
}
