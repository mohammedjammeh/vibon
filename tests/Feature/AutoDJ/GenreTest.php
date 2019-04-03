<?php

namespace Tests\Feature\AutoDJ;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Vibe;
use App\Track;
use App\Genre;
use App\AutoDJ\Genre as AutoGenre;

class GenreTest extends TestCase
{
	use WithFaker, RefreshDatabase;

	public function test_genres_of_track_can_be_stored()
	{
		$track = factory(Track::class)->create();
		$track->genres()->detach();		
		AutoGenre::store($track);
		$this->assertNotEmpty($track->genres);
	}

	public function test_tracks_are_ordered_by_popularity_of_genres_within_a_vibe()
	{
		$vibe = factory(Vibe::class)->create();

		$loveTrack = factory(Track::class)->create();
		$danceTrack = factory(Track::class)->create();
		$trapTrack = factory(Track::class)->create();

		$vibe->tracks()->sync([
			$loveTrack->id => ['auto_related' => true], 
			$danceTrack->id => ['auto_related' => true],
			$trapTrack->id  => ['auto_related' => true],
		]);

		$reggaeGenre = factory(Genre::class)->create(['name' => 'reggae']);
		$rapGenre = factory(Genre::class)->create(['name' => 'rap']);

		$reggaeGenre->tracks()->sync([$loveTrack->id, $danceTrack->id]);
		$rapGenre->tracks()->sync([$trapTrack->id]);

		$tracks = AutoGenre::orderTracksByPopularity($vibe);
		$lastTrackOnGenreOrder = collect($tracks)->last()->api_id;
		$trackThatBelongsToAnUnpopularGenre = $trapTrack->api_id;
		$this->assertEquals($lastTrackOnGenreOrder, $trackThatBelongsToAnUnpopularGenre);
	}
}
