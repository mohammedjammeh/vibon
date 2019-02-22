<?php

namespace Tests\Feature;

use App\User;
use App\Vibe;
use App\Music\InterfaceAPI;
use App\Music\Playlist;
use App\Music\Fake\WebAPI as FakeAPI;

use Tests\TestCase;
use Tests\Feature\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VibeTest extends TestCase
{
	use WithFaker, RefreshDatabase;

	/** @test */
	public function a_user_can_create_a_vibe()
	{
		app()->bind(InterfaceAPI::class, FakeAPI::class);
		$playlist = app(Playlist::class);
		$name = $this->faker->word;
		$newPlaylist = $playlist->create($name);

		$attributes = factory(Vibe::class)->raw(['name' => $name, 'api_id' => $newPlaylist->id]);

		$this->post('/vibe', $attributes)->assertRedirect(Vibe::first()->path());
		$this->assertDatabaseHas('vibes', $attributes);
		$this->get(Vibe::first()->path())->assertSee($attributes['name']);
	}

	/** @test */
	public function a_vibe_requires_a_name_and_a_description()
	{
		$vibe = factory(Vibe::class)->raw(['name' => '']);
		$this->post('/vibe', $vibe)->assertSessionHasErrors('name');

		$vibe = factory(Vibe::class)->raw(['description' => '']);
		$this->post('/vibe', $vibe)->assertSessionHasErrors('description');
	}

	/** @test */
	public function a_vibe_can_be_viewed_by_the_user()
	{
		$this->withoutExceptionHandling();
		$vibe = factory(Vibe::class)->create();
		$this->get($vibe->path())
			->assertSee($vibe->name)
			->assertSee($vibe->description);
	}

	
	public function a_vibe_is_sent_to_the_right_location()
	{
		// Assert that the response has the given URI value in the Location header:
		// $response->assertLocation($uri);
	}

	/** @test */
	public function a_vibe_has_all_required_data()
	{
		// $response->assertViewHasAll(array $data);
	}

	/** @test */
	public function a_vibe_is_that_is_meant_to_be_shown_is_getting_displayed() 
	{
		// $response->assertViewIs($value);
	}

	/** @test */
	public function a_vibe_that_has_been_deleted_is_not_found()
	{
		// Assert that the response has a not found status code:
		// $response->assertNotFound();
	}


}
