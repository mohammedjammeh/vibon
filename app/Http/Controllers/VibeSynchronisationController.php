<?php

namespace App\Http\Controllers;

use App\MusicAPI\Playlist;
use App\Traits\VibeShowTrait;
use App\Vibe;
use App\Track;
use App\AutoDJ\Genre as AutoGenre;
use App\AutoDJ\Tracks as AutoTracks;

class VibeSynchronisationController extends Controller
{
    use VibeShowTrait;

    public function __construct()
    {
        $this->middleware('authenticated');
    }

    public function updatePlaylistTracks(Vibe $vibe)
    {
        $vibeTracksIDs = $vibe->showTracks->pluck('api_id')->toArray();
        app(Playlist::class)->replaceTracks($vibe, $vibeTracksIDs);

        $loadedVibe = app(Playlist::class)->load($vibe);
        $message = $loadedVibe->name . ' has been synced using vibe tracks.';
        return $this->showResponse($loadedVibe, $message);
    }

    public function updateVibeTracks(Vibe $vibe)
    {
        $vibe->tracks()->where('auto_related', $vibe->auto_dj)->detach();

        $playlistTracks = collect(app(Playlist::class)->get($vibe->api_id)->tracks->items)
            ->pluck('track');
        $playlistTracksIDs = $playlistTracks->pluck('id');
        $this->storeUnstoredPlaylistTracks($playlistTracks);

        $playlistTracksVibeIDs = Track::whereIn('api_id', $playlistTracksIDs)->get()->pluck('id');
        $vibe->tracks()->attach($playlistTracksVibeIDs, ['auto_related' => $vibe->auto_dj]);

        AutoTracks::updateAPI($vibe);

        $loadedVibe = app(Playlist::class)->load($vibe);
        $message = $loadedVibe->name . ' has been synced using playlist tracks.';
        return $this->showResponse($loadedVibe, $message);
    }

    public function storeUnstoredPlaylistTracks($playlistTracks)
    {
        $playlistTracksNotOnVibe = $playlistTracks->whereNotIn('id', Track::pluck('api_id'));
        $playlistTracksNotOnVibe->each(function ($track) {
            $track = Track::create(['api_id' => $track->id]);
            AutoGenre::store($track);
        });
    }
}
