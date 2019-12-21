<?php

namespace App\Http\Controllers;

use App\MusicAPI\Playlist;
use App\Track;
use App\Traits\VibeShowTrait;
use App\Vibe;
use App\Vote;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    use VibeShowTrait;

    public function __construct()
    {
        $this->middleware('setAccessToken');
    }

    public function store(Vibe $vibe, Track $track)
    {
        $rangeStart = $vibe->showTracks->where('id', $track->id)->keys()->first();

        Vote::create([
            'user_id' => auth()->user()->id,
            'track_id' => $track->id,
            'vibe_id' => $vibe->id
        ]);

        $insertBefore = $vibe->showTracks->where('id', $track->id)->keys()->first();

        app(Playlist::class)->reorderTracks($vibe, $rangeStart, $insertBefore);

        $loadedVibe = app(Playlist::class)->load($vibe);
        return $this->showResponse($loadedVibe);
    }

    public function destroy(Vibe $vibe, Track $track)
    {
        $rangeStart = $vibe->showTracks->where('id', $track->id)->keys()->first();

        Vote::where('vibe_id', $vibe->id)
            ->where('track_id', $track->id)
            ->where('user_id', auth()->user()->id)
            ->delete();

        $insertBefore = $vibe->showTracks->where('id', $track->id)->keys()->first();

        app(Playlist::class)->reorderTracks($vibe, $rangeStart, $insertBefore + 1);

        $loadedVibe = app(Playlist::class)->load($vibe);
        return $this->showResponse($loadedVibe);
    }
}
