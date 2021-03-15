<?php

namespace App\Http\Controllers\PendingVibeTrack;

use App\Events\PendingDetachVibeTrackCreated;
use App\Events\PendingDetachVibeTrackDeleted;
use App\Events\PendingAttachVibeTracksAccepted;
use App\Events\PendingAttachVibeTracksRejected;
use App\Events\PendingDetachVibeTracksRespondedTo;
use App\MusicAPI\Playlist;
use App\PendingVibeTrack;
use App\Track;
use App\Traits\VibeShowTrait;
use App\Vibe;
use App\Http\Controllers\Controller;

class DetachController extends Controller
{
    use VibeShowTrait;

    public function store(Vibe $vibe, Track $track)
    {
        $this->authorize('update', $vibe);

        $pendingVibeTrack = PendingVibeTrack::create([
            'track_id' => $track->id,
            'vibe_id' => $vibe->id,
            'user_id' => auth()->user()->id,
            'attach' => false
        ]);

        broadcast(new PendingDetachVibeTrackCreated($pendingVibeTrack))->toOthers();
        $loadedVibe = app(Playlist::class)->load($vibe);
        return $this->showResponseWithTrack($loadedVibe, $track);
    }

    public function destroy(PendingVibeTrack $pendingVibeTrack)
    {
        $this->authorize('delete', $pendingVibeTrack);

        broadcast(new PendingDetachVibeTrackDeleted($pendingVibeTrack))->toOthers();

        $pendingVibeTrack->delete();

        $loadedVibe = app(Playlist::class)->load($pendingVibeTrack->vibe);
        return $this->showResponseWithTrack($loadedVibe, $pendingVibeTrack->track);
    }

    public function respond(Vibe $vibe)
    {
        $this->authorize('delete', $vibe);

        $responses = request()->input();
        $pendingVibeTracks = PendingVibeTrack::where('attach', false)->where('vibe_id', $vibe->id)->get();

        $vibe->tracks()
            ->wherePivot('auto_related', false)
            ->wherePivotIn('track_id', $responses['accepted'])
            ->detach();

        event(new PendingDetachVibeTracksRespondedTo($pendingVibeTracks, $responses));

        $loadedVibe = app(Playlist::class)->load($vibe);
        return $this->showResponse($loadedVibe);
    }
}
