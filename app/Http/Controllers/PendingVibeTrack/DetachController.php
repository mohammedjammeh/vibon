<?php

namespace App\Http\Controllers\PendingVibeTrack;

use App\Events\PendingDetachVibeTrackCreated;
use App\Events\PendingDetachVibeTrackDeleted;
use App\Events\PendingVibeTrackAccepted;
use App\Events\PendingVibeTrackRejected;
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

    public function accept(PendingVibeTrack $pendingVibeTrack)
    {
        $this->authorize('respond', $pendingVibeTrack);

        broadcast(new PendingVibeTrackAccepted($pendingVibeTrack))->toOthers();

        $pendingVibeTrack->delete();
        $pendingVibeTrack->vibe->tracks()
            ->wherePivot('auto_related', false)
            ->wherePivot('track_id', $pendingVibeTrack->track->id)
            ->detach();

        $loadedVibe = app(Playlist::class)->load($pendingVibeTrack->vibe);
        return $this->showResponse($loadedVibe);
    }

    public function reject(PendingVibeTrack $pendingVibeTrack)
    {
        $this->authorize('respond', $pendingVibeTrack);

        broadcast(new PendingVibeTrackRejected($pendingVibeTrack))->toOthers();

        $pendingVibeTrack->delete();

        $loadedVibe = app(Playlist::class)->load($pendingVibeTrack->vibe);
        return $this->showResponse($loadedVibe);
    }
}