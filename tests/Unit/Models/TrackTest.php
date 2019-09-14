<?php

namespace Tests\Unit\Models;

use App\Vote;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Vibe;
use App\User;
use App\Track;

class TrackTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_the_auto_related_to_scope_is_a_query_to_get_the_auto_related_tracks_of_a_vibe()
    {
        $vibe = factory(Vibe::class)->create();
        $tracks = factory(Track::class, 3)->create();
        $tracksIDs = $tracks->pluck('id');
        $vibe->tracks()->attach($tracksIDs, ['auto_related' => true]);

        $autoRelatedTracks = Track::autoRelatedTo($vibe)->get();
        $this->assertEquals($tracks->count(), $autoRelatedTracks->count());
        foreach($autoRelatedTracks as $autoRelatedTrack) {
            $this->assertContains($autoRelatedTrack->id, $tracksIDs);
        }
    }

    public function test_the_scope_belongs_to_members_of_is_a_query_to_get_tracks_that_belongs_to_members_of_a_vibe()
    {
        $vibe = factory(Vibe::class)->create();
        $users = factory(User::class, 2)->create();
        $tracks01 = factory(Track::class, 3)->create();
        $tracks02 = factory(Track::class, 3)->create();
        $tracks = $tracks01->merge($tracks02);

        $vibe->users()->attach($users->pluck('id'), ['owner' => false]);
        $users->first()->tracks()->attach($tracks01->pluck('id'), ['type' => true]);
        $users->last()->tracks()->attach($tracks02->pluck('id'), ['type' => true]);

        $vibeMembersTracks = Track::belongsToMembersOf($vibe)->get();

        $this->assertEquals($tracks->count(), $vibeMembersTracks->count());
        foreach($vibeMembersTracks as $vibeMemberTrack) {
            $this->assertContains($vibeMemberTrack->id, $tracks->pluck('id'));
        }
    }

    public function test_the_votes_count_on_method_returns_the_count_for_the_number_of_votes_of_track_on_a_vibe()
    {
        $track = factory(Track::class)->create();
        $vibe = factory(Vibe::class)->create();
        $votesCount = 3;

        for ($count = 0; $count < $votesCount; $count++) {
            factory(Vote::class)->create([
                'user_id' => factory(User::class)->create(),
                'track_id' => $track->id,
                'vibe_id' => $vibe->id,
            ]);
        }

        $this->assertEquals($track->votesCountOn($vibe), $votesCount);
    }

    public function test_the_is_voted_by_auth_user_on_method_checks_if_a_user_has_voted_has_voted_for_a_track()
    {
        $track = factory(Track::class)->create();
        $vibe = factory(Vibe::class)->create();

        factory(Vote::class)->create([
            'user_id' => $this->user,
            'track_id' => $track->id,
            'vibe_id' => $vibe->id,
        ]);

        $this->assertTrue($track->isVotedByAuthUserOn($vibe));

        $newUser = factory(User::class)->create();
        $this->actingAs($newUser);
        $this->assertFalse($track->isVotedByAuthUserOn($vibe));
    }
}
