<?php

namespace Tests\Feature\Controller;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Vibe;
use App\Track;

class TrackVibeTest extends TestCase
{
	use WithFaker, RefreshDatabase;

    public function test_track_can_be_added_to_a_vibe()
    {
    	$track = factory(Track::class)->create();
    	$vibe = factory(Vibe::class)->create();
    	$attributes = ['track-api-id' => $track->api_id];
    	$this->post(route('track-vibe.store', $vibe), $attributes);
    	$this->assertDatabaseHas('track_vibe', [
    		'track_id' => $track->id,
    		'vibe_id' => $vibe->id,
    		'auto_related' => false
    	]);
    }

    public function test_new_track_can_be_added_to_a_vibe()
    {
    	$vibe = factory(Vibe::class)->create();
    	$attributes = ['track-api-id' => '328WHEW92NWI21'];
    	$this->post(route('track-vibe.store', $vibe), $attributes);
    	$track = Track::where('api_id', $attributes['track-api-id'])->first();
    	$this->assertDatabaseHas('track_vibe', [
    		'track_id' => $track->id,
    		'vibe_id' => $vibe->id,
    		'auto_related' => false
    	]);
    }

    public function test_track_can_be_removed_from_a_vibe()
    {
        $vibe = factory(Vibe::class)->create();
        $track = $vibe->tracks()->first();
        $this->delete(route('track-vibe.destroy', [
            'vibe' => $vibe->id, 
            'track' => $track->id
        ]));
        $this->assertDatabaseMissing('track_vibe', [
            'track' => $track->id,
            'vibe' => $vibe->id,
            'owner' => false
        ]);
    }
}
