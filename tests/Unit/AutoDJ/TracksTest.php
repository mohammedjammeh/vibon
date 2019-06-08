<?php

namespace Tests\Unit\AutoDJ;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Vibe;
use App\Track;
use App\AutoDJ\Tracks as AutoTracks;
use App\Events\VibeCreated;
use App\Music\User as UserAPI;

class TracksTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_auto_tracks_of_a_vibe_which_belongs_to_its_users_can_be_stored()
    {
        $user = factory(User::class)->create();
        $tracks = factory(Track::class, 2)->create();
        $tracksIDs = $tracks->pluck('id')->toArray();
        $vibe = factory(Vibe::class)->create();
        $user->tracks()->attach($tracksIDs, ['type' => UserAPI::TOP_TRACK]);
        $vibe->users()->attach($user->id, ['owner' => true]);

        AutoTracks::store($vibe);
        $vibeAutoTracks = $vibe->tracks()->where('auto_related', true)->get()->pluck('api_id');
		$vibeLoad = $vibe->load('users.tracks');
		$vibeUsersTracks = $vibeLoad['tracks']->pluck('api_id');
		foreach ($vibeAutoTracks as $autoTrack) {
			$this->assertContains($autoTrack, $vibeUsersTracks);
		}
    }

    public function test_auto_tracks_of_a_vibe_which_belongs_to_its_users_can_be_updated()
    {
        $user = factory(User::class)->create();
    	$vibe = factory(Vibe::class)->create();
        $vibe->users()->attach($user->id, ['owner' => true]);

        $vibeTrack = factory(Track::class)->create();
        $vibe->tracks()->attach($vibeTrack->id, ['auto_related' => true]);

    	$userTrack = factory(Track::class)->create();
    	$user->tracks()->attach($userTrack->id, ['type' => UserAPI::TOP_TRACK]);

        event(new VibeCreated($vibe));
    	AutoTracks::update($vibe);
    	$vibeAutoTracks = $vibe->tracks()->where('auto_related', true)->get()->pluck('api_id');
        $this->assertContains($userTrack->api_id, $vibeAutoTracks);
    	$this->assertNotContains($vibeTrack->api_id, $vibeAutoTracks);
    }
}
