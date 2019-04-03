<?php

namespace Tests\Feature\AutoDJ;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Vibe;
use App\Track;
use App\AutoDJ\Tracks;
use App\Events\VibeCreated;
use App\Music\User as UserAPI;

class TracksTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_auto_tracks_of_a_vibe_which_belongs_to_its_users_can_be_stored()
    {
        $vibe = factory(Vibe::class)->create();
        app(Tracks::class)->store($vibe);

        $vibeAutoTracks = $vibe->tracks()->where('auto_related', true)->get()->pluck('api_id');

		$vibeLoad = $vibe->load('users.tracks');
		$vibeUsersTracks = $vibeLoad['tracks']->pluck('api_id');
		
		foreach ($vibeAutoTracks as $autoTrack) {
			$this->assertContains($autoTrack, $vibeUsersTracks);
		}
    }

    public function test_auto_tracks_of_a_vibe_which_belongs_to_its_users_can_be_updated()
    {
    	$vibe = factory(Vibe::class)->create();
    	event(new VibeCreated($vibe));

    	$vibeUser = $vibe->users->first();
    	$newUserTrack = factory(Track::class)->create();
    	$vibeUser->tracks()->attach($newUserTrack->id, ['type' => UserAPI::TOP_TRACK]);

    	app(Tracks::class)->update($vibe);

    	$vibeAutoTracks = $vibe->tracks()->where('auto_related', true)->get()->pluck('api_id');
    	$this->assertContains($newUserTrack->api_id, $vibeAutoTracks);
    }
}
