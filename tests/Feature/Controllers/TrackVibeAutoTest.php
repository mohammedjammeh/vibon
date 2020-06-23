<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\User;
use App\Vibe;
use App\Track;
use App\AutoDJ\Tracks;
use App\MusicAPI\User as UserAPI;

class TrackVibeAutoTest extends TestCase
{
	use WithFaker, RefreshDatabase;

	public function test_auto_tracks_of_a_vibe_can_be_updated() 
	{
		$user = factory(User::class)->create();
		$userTrack = factory(Track::class)->create();
		$user->tracks()->attach($userTrack->id, ['type' => UserAPI::TOP_TRACK]);

		$vibe = factory(Vibe::class)->create(['auto_dj' => true]);
		$vibe->users()->attach($user->id, ['owner' => true]);

		$vibeTrack = factory(Track::class)->create();
		$vibe->tracks()->attach($vibeTrack->id, ['auto_related' => true]);

		$this->post(route('auto-vibe.refresh', $vibe));
		$vibeTracks = $vibe->tracks->pluck('api_id')->toArray();
		$this->assertNotContains($vibeTrack->api_id, $vibeTracks);
		$this->assertContains($userTrack->api_id, $vibeTracks);
	}
}
