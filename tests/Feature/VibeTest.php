<?php

namespace Tests\Feature;

use App\User;
use App\Music\WebAPI;
use App\Music\Playlist;
use App\Music\FakeAPI;

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

		$vibe = [];

		$vibe['name'] = $this->faker->word;
		

		$fakeAPI = new FakeAPI();




		$mockAPI = $this->getMockBuilder(FakeAPI::class)->getMock();

		$mockAPI->expects($this->any())

		    ->method('createPlaylist')

		    ->will($this->returnValue($fakeAPI->createPlaylist($vibe)));





		$vibe['api_id'] = $mockAPI->createPlaylist($vibe)->id;

		$vibe['description'] = $this->faker->paragraph;

		$vibe['open'] = $this->faker->boolean();

		$vibe['auto_dj'] = $this->faker->boolean();



		$this->post('/vibe', $vibe);

		$this->assertDatabaseHas('vibes', $vibe);

	}

}
