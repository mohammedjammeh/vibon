<?php

namespace Tests\Feature;

use App\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

use App\Spotify\Playlist;
use App\Spotify\WebAPI;

class VibeTest extends TestCase

{

	use WithFaker, RefreshDatabase, WithoutMiddleware;


	/** @test */
	public function a_user_can_create_a_vibe()

	{

		$this->withoutExceptionHandling();

		$this->user = factory(User::class)->create();

		$this->actingAs($this->user);





		$attributes = [

			'title' => $this->faker->word,

			'api_id' => '43AkVNhyj923bn0FHw0RC',

			'description' => $this->faker->paragraph,

			'type' => $this->faker->boolean(),

			'auto_dj' => $this->faker->boolean()

		];	



		$api = new WebAPI;

		$api->authorise();



		$playlist = new Playlist();

		$newPlaylist = $playlist->create($attributes['title']);

		$attributes[] = array('api_id' => $newPlaylist->id);




		$this->post('vibe', $attributes);

		$this->assertDatabaseHas('vibes', $attributes);

	}

}
