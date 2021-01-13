<?php

namespace Tests\Feature\Controllers;

use App\User;
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
    	$vibe->users()->attach($this->user->id, ['owner' => true]);

    	$response = $this->post(route('track-vibe.store', [$vibe, $track->api_id]));
        $responseData = $response->original;

        $this->assertEquals('', $responseData['message']);
        $this->assertEquals($vibe->id, $responseData['vibe']->id);
    	$this->assertDatabaseHas('track_vibe', [
    		'track_id' => $track->id,
    		'vibe_id' => $vibe->id,
    		'user_id' => $vibe->owner->id,
    		'auto_related' => false
    	]);
    }

    public function test_new_track_can_be_added_to_a_vibe()
    {
        $newTrackApiId = '328WHEW92NWI21';
    	$vibe = factory(Vibe::class)->create();
        $vibe->users()->attach($this->user->id, ['owner' => true]);

    	$response = $this->post(route('track-vibe.store', [$vibe, $newTrackApiId]));
        $responseData = $response->original;
        $track = Track::where('api_id', $newTrackApiId)->first();

        $this->assertEquals('', $responseData['message']);
        $this->assertEquals($vibe->id, $responseData['vibe']->id);
    	$this->assertDatabaseHas('track_vibe', [
    		'track_id' => $track->id,
    		'vibe_id' => $vibe->id,
            'user_id' => $vibe->owner->id,
    		'auto_related' => false
    	]);
    }

    public function test_track_can_be_removed_from_a_vibe()
    {
        $vibe = factory(Vibe::class)->create();
        $track = factory(Track::class)->create();
        $vibe->tracks()->attach($track->id, ['auto_related' => false]);
        $vibe->users()->attach($this->user->id, ['owner' => true]);

        $response = $this->delete(route('track-vibe.destroy', [
            'vibe' => $vibe->id,
            'track' => $track->id
        ]));
        $responseData = $response->original;

        $this->assertEquals('', $responseData['message']);
        $this->assertEquals($vibe->id, $responseData['vibe']->id);
        $this->assertDatabaseMissing('track_vibe', [
            'track' => $track->id,
            'vibe' => $vibe->id,
            'owner' => false
        ]);
    }
}
