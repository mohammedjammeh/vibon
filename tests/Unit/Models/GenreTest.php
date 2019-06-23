<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Vibe;
use App\Genre;
use App\Track;

class GenreTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_the_with_tracks_auto_related_scope_gets_the_genres_with_tracks_that_are_auto_related_to_a_vibe__and_the_tracks_count()
    {
        $vibe = factory(Vibe::class)->create();
        $genre = factory(Genre::class)->create();
        $tracks = factory(Track::class, 3)->create();
        $tracksIDs = $tracks->pluck('id');
        $vibe->tracks()->attach($tracksIDs, ['auto_related' => true]);
        $genre->tracks()->attach($tracksIDs, []);

        $genres = Genre::withTracksAutoRelated($vibe)->get();
        $this->assertEquals($genres->first()->tracks_count, $tracks->count());
        foreach ($genres->first()->tracks()->get() as $genreTrack) {
            $this->assertContains($genreTrack->id, $tracksIDs);
        }
    }

    public function test_the_order_by_popularity_scope_is_a_query_to_get_genres_of_vibe_and_orders_them_based_on_their_track_count_from_highest_to_lowest()
    {
        $rapTracks = factory(Track::class, 3)->create();
        $reggaeTracks = factory(Track::class, 5)->create();
        $rockTracks = factory(Track::class, 8)->create();
        $tracks = $reggaeTracks->concat($rapTracks)->concat($rockTracks);

        $vibe = factory(Vibe::class)->create();
        $vibe->tracks()->attach($tracks->pluck('id'), ['auto_related' => true]);

        $rapGenre = factory(Genre::class)->create();
        $rapGenre->tracks()->attach($rapTracks->pluck('id'), []);

        $reggaeGenre = factory(Genre::class)->create();
        $reggaeGenre->tracks()->attach($reggaeTracks->pluck('id'), []);

        $rockGenre = factory(Genre::class)->create();
        $rockGenre->tracks()->attach($rockTracks->pluck('id'), []);

        $genres = Genre::orderByPopularity($vibe)->get();
        $this->assertEquals($genres->first()->id, $rockGenre->id);
        $this->assertEquals($genres[1]->id, $reggaeGenre->id);
        $this->assertEquals($genres->last()->id, $rapGenre->id);
    }
}
