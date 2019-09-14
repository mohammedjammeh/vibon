<?php

namespace Tests\Unit\Models;

use App\Genre;
use App\Track;
use App\Vote;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Vibe;
use App\JoinRequest;

class VibeTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_the_show_tracks_method_gets_the_right_tracks()
    {
        $vibe = factory(Vibe::class)->create();

        $autoTracks = factory(Track::class, 3)->create();
        $autoTracks->map(function ($track) {
            $genres = factory(Genre::class, 2)->create();
            return $track->genres()->attach($genres->pluck('id'), []);
        });
        $vibe->tracks()->attach($autoTracks->pluck('id'), ['auto_related' => true]);

        $tracks = factory(Track::class, 4)->create();
        $vibe->tracks()->attach($tracks->pluck('id'), ['auto_related' => false]);

        $vibe->auto_dj = true;
        $vibe->save();
        $this->assertEquals($vibe->showTracks->count(), $autoTracks->count());
        foreach ($vibe->showTracks as $track) {
            $this->assertContains($track->id, $autoTracks->pluck('id'));
        }

        $vibe->auto_dj = false;
        $vibe->save();
        $this->assertEquals($vibe->showTracks->count(), $tracks->count());
        foreach ($vibe->showTracks as $track) {
            $this->assertContains($track->id, $tracks->pluck('id'));
        }
    }

    public function test_the_show_tracks_method_for_a_manual_vibe_orders_tracks_based_on_their_number_of_votes_and_relationship_created_at_date()
    {
        $vibe = factory(Vibe::class)->create(['auto_dj' => false]);
        $tracks = factory(Track::class, 2)->create();
        $vibe->tracks()->attach($tracks->pluck('id'), ['auto_related' => false]);

        factory(Vote::class, 2)->create([
            'vibe_id' => $vibe->id,
            'track_id' => $tracks->first()->id
        ]);

        sleep(1);
        $newTrack = factory(Track::class)->create();
        $vibe->tracks()->attach($newTrack->id, ['auto_related' => false]);

        $this->assertEquals($vibe->showTracks->first()->id, $tracks->first()->id);
        $this->assertEquals($vibe->showTracks->last()->id, $newTrack->id);

    }

    public function test_the_has_member_function_checks_if_a_user_is_a_member_of_a_vibe_or_not()
    {
        $vibe = factory(Vibe::class)->create();

        $nonMember = factory(User::class)->create();
        $this->assertEquals($vibe->hasMember($nonMember), false);

        $newVibe = factory(Vibe::class)->create();
        $member = factory(User::class)->create();
        $newVibe->users()->attach($member->id, ['owner' => false]);
        $this->assertEquals($newVibe->hasMember($member), true);
    }

    public function test_the_has_join_request_from_method_checks_if_a_vibe_has_a_join_request_from_a_user_or_not()
    {
        $vibe = factory(Vibe::class)->create();
        $user = factory(User::class)->create();
        $this->assertEquals($vibe->hasJoinRequestFrom($user), false);

        $newVibe = factory(Vibe::class)->create();
        $newUser = factory(User::class)->create();
        factory(JoinRequest::class)->create([
            'vibe_id' => $newVibe,
            'user_id' => $newUser->id
        ]);
        $this->assertEquals($newVibe->hasJoinRequestFrom($newUser), true);
    }

    public function test_the_join_request_from_method_gets_the_join_request_from_given_user()
    {
        $vibe = factory(Vibe::class)->create();
        $user = factory(User::class)->create();
        $joinRequest = factory(JoinRequest::class)->create([
            'vibe_id' => $vibe,
            'user_id' => $user->id
        ]);
        $this->assertEquals($vibe->joinRequestFrom($user)->id, $joinRequest->id);
        $this->assertEquals($vibe->joinRequestFrom($user)->vibe_id, $vibe->id);
        $this->assertEquals($vibe->joinRequestFrom($user)->user_id, $user->id);
    }

    public function test_the_get_owner_attribute_method_gets_the_owner_of_a_vibe()
    {
        $vibe = factory(Vibe::class)->create();
        $user = factory(User::class)->create();
        $vibe->users()->attach($user->id, ['owner' => true]);

        $this->assertEquals($vibe->owner->id, $user->id);
        $this->assertEquals($vibe->owner->username, $user->username);
    }

    public function test_the_get_path_attribute_method_gets_the_path_of_a_vibe()
    {
        $vibe = factory(Vibe::class)->create();
        $this->assertEquals($vibe->path, route('vibe.show', $vibe));
    }
}
