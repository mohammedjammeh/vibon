<?php

namespace App\Http\Controllers;

use App\Events\PendingVibeTrackAccepted;
use App\Events\PendingVibeTrackCreated;
use App\Events\PendingVibeTrackDeleted;
use App\Events\PendingVibeTrackRejected;
use App\MusicAPI\Playlist;
use App\PendingVibeTrack;
use App\Track;
use App\Traits\VibeShowTrait;
use App\Vibe;
use Illuminate\Http\Request;

class PendingVibeTrackController extends Controller
{
    use VibeShowTrait;

    public function store(Vibe $vibe, Track $track)
    {
        $this->authorize('update', $vibe);

        $pendingVibeTrack = PendingVibeTrack::create([
            'track_id' => $track->id,
            'vibe_id' => $vibe->id,
            'user_id' => auth()->user()->id
        ]);

        broadcast(new PendingVibeTrackCreated($pendingVibeTrack))->toOthers();

        $loadedVibe = app(Playlist::class)->load($vibe);
        return $this->showResponse($loadedVibe);
    }

    public function destroy(PendingVibeTrack $pendingVibeTrack)
    {
        $this->authorize('delete', $pendingVibeTrack);

        broadcast(new PendingVibeTrackDeleted($pendingVibeTrack))->toOthers();
        $pendingVibeTrack->delete();

        $loadedVibe = app(Playlist::class)->load($pendingVibeTrack->vibe);
        return $this->showResponse($loadedVibe);
    }

    public function accept(PendingVibeTrack $pendingVibeTrack)
    {
        $this->authorize('delete', $pendingVibeTrack);

        // notification
        broadcast(new PendingVibeTrackAccepted($pendingVibeTrack))->toOthers();

        $pendingVibeTrack->delete();
        $pendingVibeTrack->vibe->tracks()
            ->attach($pendingVibeTrack->track->id, ['auto_related' => false]);

        $loadedVibe = app(Playlist::class)->load($pendingVibeTrack->vibe);
        return $this->showResponse($loadedVibe);
    }

    public function reject(PendingVibeTrack $pendingVibeTrack)
    {
        $this->authorize('delete', $pendingVibeTrack);

        // notification
        broadcast(new PendingVibeTrackRejected($pendingVibeTrack))->toOthers();

        $pendingVibeTrack->delete();

        $loadedVibe = app(Playlist::class)->load($pendingVibeTrack->vibe);
        return $this->showResponse($loadedVibe);
    }
}
