<?php

namespace Tests\Feature;

use App\Genre;
use App\Vibe;
use Tests\TestCase;
use Tests\Feature\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VibeGenresTest extends TestCase
{
	use WithFaker, RefreshDatabase;
	
	public function test_i_can_get_one_genres_of_a_vibe() {
		// Creating all the data necessary to get genres
		// TODO simplify to use factories
		$genre = Genre::create(['name' => 'Raggae']);
		$vibe = factory(Vibe::class)->create();
		$track = $vibe->tracks()->create([
			'api_id' => $this->faker->uuid
		]);
		$track->genres()->attach($genre->id);

		$genres = Genre::fromVibe($vibe);

		$this->assertContains('Raggae', $genres);
	}
	
	public function test_i_can_get_all_genres_of_a_vibe() {
		// Creating all the data necessary to get genres
		// TODO simplify to use factories
		$genres = [];
		$genres[] = Genre::create(['name' => 'Raggae'])->id;
		$genres[] = Genre::create(['name' => 'Jazz'])->id;
		$genres[] = Genre::create(['name' => 'Pop'])->id;

		$vibe = factory(Vibe::class)->create();
		$track = $vibe->tracks()->create([
			'api_id' => $this->faker->uuid
		]);
		$track->genres()->attach($genres);

		$genres = Genre::fromVibe($vibe);

		$this->assertCount(3, $genres);
		$this->assertContains('Raggae', $genres);
		$this->assertContains('Jazz', $genres);
	}

}