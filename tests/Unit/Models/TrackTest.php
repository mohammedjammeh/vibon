<?php

namespace Tests\Unit\Models;

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
}
