<?php

namespace Tests\Feature\Controller;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Vibe;
use App\Track;
use App\AutoDJ\Tracks;

class TrackVibeAutoTest extends TestCase
{
	use WithFaker, RefreshDatabase;

	public function test_auto_tracks_of_a_vibe_can_be_updated() 
	{
		$this->withoutExceptionHandling();
		$vibe = factory(Vibe::class)->create(['auto_dj' => true]);
		$track = factory(Track::class)->create(['api_id' => 'Z10Z']);
		$vibe->tracks()->attach($track->id, ['auto_related' => true]);

		$this->post(route('track-vibe-auto.update', $vibe));
		$vibeTracks = $vibe->tracks->pluck('api_id')->toArray();
		$this->assertNotContains($track->api_id, $vibeTracks);

		$vibeFirstUserTrack = $vibe->users->first()->tracks->first()->api_id;
		$vibeLastUserTrack = $vibe->users->last()->tracks->first()->api_id;
		$this->assertContains($vibeFirstUserTrack, $vibeTracks);
		$this->assertContains($vibeLastUserTrack, $vibeTracks);
	}
}
