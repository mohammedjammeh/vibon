<?php

namespace App\Http\Controllers;

use App\MusicAPI\Playlist;
use App\Track;
use App\Vibe;
use App\Vote;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function store(Vibe $vibe, Track $track)
    {
        $rangeStart = $vibe->showTracks->where('id', $track->id)->keys()->first();

        Vote::create([
            'user_id' => auth()->user()->id,
            'track_id' => $track->id,
            'vibe_id' => $vibe->id
        ]);

        $insertBefore = $vibe->showTracks->where('id', $track->id)->keys()->first();

        app(Playlist::class)->reorderTracks($vibe->api_id, $rangeStart, $insertBefore);
        return back();
    }

    public function destroy(Vibe $vibe, Track $track)
    {
        $rangeStart = $vibe->showTracks->where('id', $track->id)->keys()->first();

        Vote::where('vibe_id', $vibe->id)
            ->where('track_id', $track->id)
            ->where('user_id', auth()->user()->id)
            ->delete();

        $insertBefore = $vibe->showTracks->where('id', $track->id)->keys()->first();

        app(Playlist::class)->reorderTracks($vibe->api_id, $rangeStart, $insertBefore + 1);
        return back();
    }
}
