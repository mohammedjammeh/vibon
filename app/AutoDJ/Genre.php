<?php 

namespace App\AutoDJ;

use App\Music\Artist;
use App\Genre as GenreModel;
use App\Music\Tracks;

class Genre
{
    public static function store($track) 
    {
        $loadedTrack = app(Tracks::class)->load([$track])[0];
        $genreIDs = [];
        foreach ($loadedTrack->artists as $artist) {
            $genres = app(Artist::class)->get($artist->id)->genres;
            foreach ($genres as $genre) {
                $trackGenre = GenreModel::firstOrCreate(['name' => $genre]);
                $genreIDs[] = $trackGenre->id;
            }
        }
        $track->genres()->sync(array_unique($genreIDs));
    }

    public static function orderTracksByPopularityForAPI($vibe)
    {
        $genres = GenreModel::orderByPopularity($vibe)->get()->pluck('tracks');
        $tracks = [];
        foreach ($genres as $genre) {
            foreach ($genre as $track) {
                $tracks[] = $track->api_id;
            }
        }
        $tracks = array_values(array_unique($tracks));
        return $tracks;
    }

    public static function orderTracksByPopularity($vibe)
    {
        $genres = GenreModel::orderByPopularity($vibe)->get()->pluck('tracks');
        $tracks = [];
        $trackIDs = [];
        foreach ($genres as $genre) {
            foreach ($genre as $track) {
                if (!in_array($track->id, $trackIDs)) {
                   $tracks[] = $track; 
                   $trackIDs[] = $track->id;
                }
            }
        }
        return $tracks;
    }
}