<?php // used in trackVibeController, AutoTracks and Vibe Model

namespace App\AutoDJ;

use App\Music\Artist;
use App\Genre as GenreModel;
use App\Music\Tracks;
use Illuminate\Support\Arr;

class Genre
{
    public static function store($track) 
    {
        $genresIDs = collect([]);
        $artistsGenres = collect([]);
        $loadedTrack = app(Tracks::class)->load([$track])[0];

        $artistsIDs = collect($loadedTrack->artists)->pluck('id');
        $artistsIDs->each(function ($artistID) use($artistsGenres) {
            $artistsGenres[] = app(Artist::class)->get($artistID)->genres;
        });

        $genres = collect(Arr::flatten($artistsGenres))->unique()->values()->all();
        collect($genres)->each(function ($genre) use($genresIDs) {
            $trackGenre = GenreModel::firstOrCreate(['name' => $genre]); 
            $genresIDs[] = $trackGenre->id;             
        });

        $track->genres()->sync($genresIDs);
    }

    public static function orderTracksByPopularityForAPI($vibe)
    {
        $genres = GenreModel::orderByPopularity($vibe)->get()->pluck('tracks');
        $allTracks = collect($genres)->collapse()->all();
        $tracksIDs = collect($allTracks)->unique('id')->pluck('api_id')->toArray();
        return $tracksIDs;
    }

    public static function orderTracksByPopularity($vibe)
    {
        $genres = GenreModel::orderByPopularity($vibe)->get()->pluck('tracks');
        $allTracks = collect($genres)->collapse()->all();
        $tracks = collect($allTracks)->unique('id')->values();
        return $tracks;
    }
}