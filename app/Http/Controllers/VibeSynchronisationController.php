<?php

namespace App\Http\Controllers;

use App\Events\PlaylistSynchronisedWithVibeTracks;
use App\Events\VibeSynchronisedWithPlaylistTracks;
use App\MusicAPI\Playlist;
use App\Traits\VibeShowTrait;
use App\Vibe;
use App\Track;
use App\AutoDJ\Genre as AutoGenre;

class VibeSynchronisationController extends Controller
{
    use VibeShowTrait;

    public function updatePlaylistTracks(Vibe $vibe)
    {
        $vibeTracksIDs = $vibe->showTracks->pluck('api_id')->toArray();
        app(Playlist::class)->replaceTracks($vibe, $vibeTracksIDs);

        $loadedVibe = app(Playlist::class)->load($vibe);
        $message = $loadedVibe->name . ' has been synced using vibe tracks.';
        broadcast(new PlaylistSynchronisedWithVibeTracks($vibe, $message))->toOthers();
        return $this->showResponse($loadedVibe, $message);
    }

    public function updateVibeTracks(Vibe $vibe)
    {
        $vibe->tracks()->where('auto_related', $vibe->auto_dj)->detach();
        $vibe->tracks()->attach($this->getPlaylistTracksVibonIDs($vibe), ['auto_related' => $vibe->auto_dj]);

        $loadedVibe = app(Playlist::class)->load($vibe);
        $message = $loadedVibe->name . ' has been synced using playlist tracks.';
        broadcast(new VibeSynchronisedWithPlaylistTracks($vibe, $message))->toOthers();
        return $this->showResponse($loadedVibe, $message);
    }

    protected function getPlaylistTracksVibonIDs($vibe)
    {
        $playlistTracks = collect(app(Playlist::class)
            ->get($vibe->api_id)->tracks->items)
            ->pluck('track');

        $this->saveUnsavedPlaylistTracks($playlistTracks);

        return $playlistTracks->map(function ($playlistTrack)  {
            return Track::where('api_id', $playlistTrack->id)->first()->id;
        })->toArray();
    }

    protected function saveUnsavedPlaylistTracks($playlistTracks)
    {
        $playlistTracksNotOnVibon = $playlistTracks->whereNotIn('id', Track::pluck('api_id'));
        $playlistTracksNotOnVibon->each(function ($track) {
            $track = Track::create(['api_id' => $track->id]);
            AutoGenre::store($track);
        });
    }
}
