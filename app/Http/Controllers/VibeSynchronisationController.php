<?php

namespace App\Http\Controllers;

use App\MusicAPI\Playlist;
use Illuminate\Http\Request;
use App\Vibe;
use App\Track;
use App\AutoDJ\Genre as AutoGenre;
use App\AutoDJ\Tracks as AutoTracks;

class VibeSynchronisationController extends Controller
{
    public function sync(Request $request, Vibe $vibe)
    {
        if($request->input('vibe')) {
            return $this->updatePlaylistTracks($vibe);
        }
        return $this->updateVibeTracks($vibe);
    }

    public function updatePlaylistTracks($vibe)
    {
        $vibeTracksIDs = $vibe->showTracks->pluck('api_id')->toArray();
        app(Playlist::class)->replaceTracks($vibe->api_id, $vibeTracksIDs);
        return back();
    }

    public function updateVibeTracks($vibe)
    {
        $vibe->tracks()->where('auto_related', $vibe->auto_dj)->detach();

        $playlistTracks = collect(app(Playlist::class)->get($vibe->api_id)->tracks->items)
            ->pluck('track');
        $playlistTracksIDs = $playlistTracks->pluck('id');
        $this->storeUnstoredPlaylistTracks($playlistTracks);

        $playlistTracksVibeIDs = Track::whereIn('api_id', $playlistTracksIDs)->get()->pluck('id');
        $vibe->tracks()->attach($playlistTracksVibeIDs, ['auto_related' => $vibe->auto_dj]);

        AutoTracks::updateAPI($vibe);

        return back();
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
