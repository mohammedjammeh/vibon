<?php

namespace Tests\Feature;

use App\User;
use App\Vibe;
use App\Music\InterfaceAPI;
use App\Music\Playlist;
use App\Music\Fake\WebAPI as FakeAPI;
use App\Events\VibeCreated;

use Tests\TestCase;
use Tests\Feature\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VibeTest extends TestCase
{
	use WithFaker, RefreshDatabase;

	/** @test */
	public function vibe_requires_a_name()
	{
		$vibe = factory(Vibe::class)->raw(['name' => '']);
		$this->post('/vibe', $vibe)->assertSessionHasErrors('name');
	}

	/** @test */
	public function vibe_requires_a_description() 
	{
		$vibe = factory(Vibe::class)->raw(['description' => '']);
		$this->post('/vibe', $vibe)->assertSessionHasErrors('description');
	}

	/** @test */
	public function vibe_can_be_created()
	{
		Event::fake();
		$playlist = app(Playlist::class)->create('Party');
		$attributes = factory(Vibe::class)->raw([
			'name' => $playlist->name, 
			'api_id' => $playlist->id
		]);
		$this->post(route('vibe.store'), $attributes)
			->assertRedirect(Vibe::first()->path());
		$this->assertDatabaseHas('vibes', [
			'api_id' => $attributes['api_id'],
			'description' => $attributes['description']
		]);
	}

	/** @test */
	public function vibe_created_event_is_triggered_when_a_vibe_is_created()
	{
		Event::fake();
		$playlist = app(Playlist::class)->create($this->faker->word);
		$attributes = factory(Vibe::class)->raw([
			'name' => $playlist->name, 
			'api_id' => $playlist->id
		]);
		$this->post(route('vibe.store'), $attributes);
		Event::assertDispatched(VibeCreated::class);
	}

	/** @test */
	public function vibe_created_listener_ensures_that_new_vibe_has_auto_related_tracks_based_on_its_users_tracks()
	{
		$vibe = factory(Vibe::class)->create(['auto_dj' => false]);
		event(new VibeCreated($vibe));
		$vibeTracks = $vibe->tracks->pluck('api_id')->toArray();
		$vibeUserTrack = $vibe->users->first()->tracks->first()->api_id;
		$this->assertContains($vibeUserTrack, $vibeTracks);
	}

	/** @test */
	public function vibe_can_be_viewed_by_the_user()
	{
		$this->withoutExceptionHandling();
		$vibe = factory(Vibe::class)->create();
		$this->get($vibe->path())
			->assertSuccessful()
			->assertSee($vibe->description);
	}

	/** @test */
	public function vibe_view_gets_required_data()
	{
		$vibe = factory(Vibe::class)->create();
		event(new VibeCreated($vibe));
		$tracks = $vibe->showTracks();
		dd($vibe->auto_dj, $tracks);


		// $this->get($vibe->path())->assertViewHasAll([
		// 	'vibe' => app(Playlist::class)->load($vibe)
		// ]);
	}

	// /** @test */
	// public function a_vibe_is_that_is_meant_to_be_shown_is_getting_displayed() 
	// {
	// 	// $response->assertViewIs($value);
	// }

	// /** @test */
	// public function a_vibe_that_has_been_deleted_is_not_found()
	// {
	// 	// Assert that the response has a not found status code:
	// 	// $response->assertNotFound();
	// }


}
