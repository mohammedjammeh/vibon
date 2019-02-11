<?php

namespace Tests\Feature;

use App\User;
use App\Music\InterfaceAPI;
use App\Music\Playlist;
use App\Music\Fake\WebAPI as FakeAPI;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class VibeTest extends TestCase
{
	use WithFaker, RefreshDatabase, WithoutMiddleware;

	/** @test */
	public function a_user_can_create_a_vibe()
	{
		app()->bind(InterfaceAPI::class, FakeAPI::class);
		$playlist = app(Playlist::class);
		$vibeName = $this->faker->word;
		$newPlaylist = $playlist->create($vibeName);
		
		$vibe = [
			'name' => $vibeName,
			'api_id' => $newPlaylist->id,
			'description' => $this->faker->paragraph,
			'open' => $this->faker->boolean(),
			'auto_dj' => $this->faker->boolean()
		];

		$this->post('/vibe', $vibe);
		$this->assertDatabaseHas('vibes', $vibe);
	}
}
