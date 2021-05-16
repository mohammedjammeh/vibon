<?php

namespace Tests\Feature\Controllers;

use App\Vibe;
use App\User;
use App\MusicAPI\Playlist;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Access\AuthorizationException;

class VibeTest extends TestCase
{
	use WithFaker, RefreshDatabase;

	public function test_vibe_can_be_created()
	{
	    $this->withoutExceptionHandling();
		Event::fake();
		$playlist = app(Playlist::class)->create('Party', 'Everybody have to party!!');
		$attributes = factory(Vibe::class)->raw([
			'name' => $playlist->name,
			'description' => $playlist->description,
			'api_id' => $playlist->id
		]);

		$response = $this->post(route('vibe.store'), $attributes);
        $responseData = $response->original;

        $this->assertEquals('', $responseData['message']);
        $this->assertEquals(Vibe::all()->last()->id, $responseData['vibe']->id);
		$this->assertDatabaseHas('vibes', [
			'api_id' => $attributes['api_id'],
			'open' => $attributes['open'],
            'auto_dj' => $attributes['auto_dj']
		]);
	}

    public function test_vibe_cannot_be_updated_by_a_non_member()
    {
        $vibe = factory(Vibe::class)->create();
        $this->patch(route('vibe.update', $vibe), [
            'name' => 'Shaka Dance',
            'description' => 'Shakala Boom Boom',
            'open' => $vibe->open,
            'auto_dj' =>  $vibe->auto_dj
        ]);
        $this->assertDatabaseMissing('vibes', [
            'id' => $vibe->id,
            'description' => 'Shakala Boom Boom'
        ]);
    }

	public function test_vibe_can_be_updated_by_a_member()
	{
		Event::fake();
		$vibe = factory(Vibe::class)->create();
		$user = factory(User::class)->create();
		$vibe->users()->attach($user->id, ['owner' => true]);

		$this->actingAs($vibe->users->first());
		$response = $this->patch(route('vibe.update', $vibe), [
			'name' => 'Shaka Dance',
            'description' => 'Shakala Boom Boom',
         	'open' => $vibe->open,
            'auto_dj' =>  $vibe->auto_dj
        ]);
        $responseData = $response->original;

        $this->assertEquals('', $responseData['message']);
        $this->assertEquals($vibe->id, $responseData['vibe']->id);
		$this->assertDatabaseHas('vibes', [
			'id' => $vibe->id,
            'open' => $vibe->open,
            'auto_dj' =>  $vibe->auto_dj
		]);
	}

    public function test_vibe_cannot_be_deleted_by_user_who_is_not_an_owner()
    {
        $vibe = factory(Vibe::class)->create();
        $this->delete(route('vibe.destroy', $vibe))
            ->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('vibes', [
            'id' => $vibe->id,
            'open' => $vibe->open,
            'auto_dj' =>  $vibe->auto_dj
        ]);
    }

	public function test_vibe_can_be_deleted_by_owner()
	{
		$vibe = factory(Vibe::class)->create();
		$user = factory(User::class)->create();
		$vibe->users()->attach($user->id, ['owner' => true]);
		$owner = $vibe->users()->where('owner', true)->first();
		
		$this->actingAs($owner);

		$this->assertDatabaseMissing('vibes', [
			'id' => $vibe->id,
			'description' => $vibe->description
		]);
	}
}
