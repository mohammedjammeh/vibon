<?php

namespace App\Http\Controllers;

use App\Events\TrackVotedDown;
use App\Events\TrackVotedUp;
use App\MusicAPI\Playlist;
use App\Track;
use App\Traits\VibeShowTrait;
use App\Vibe;
use App\Vote;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    use VibeShowTrait;

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

        broadcast(new TrackVotedUp($vibe))->toOthers();

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

        broadcast(new TrackVotedDown($vibe))->toOthers();

        $loadedVibe = app(Playlist::class)->load($vibe);
        return $this->showResponse($loadedVibe);
    }
}
