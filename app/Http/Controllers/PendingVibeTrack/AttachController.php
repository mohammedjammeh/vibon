<?php

namespace App\Http\Controllers\PendingVibeTrack;

use App\Events\PendingVibeTrackAccepted;
use App\Events\PendingAttachVibeTrackCreated;
use App\Events\PendingAttachVibeTrackDeleted;
use App\Events\PendingVibeTrackRejected;
use App\Http\Controllers\Controller;
use App\MusicAPI\Playlist;
use App\PendingVibeTrack;
use App\Traits\VibeShowTrait;
use App\Vibe;
use App\Repositories\TrackRepo as TrackRepository;

class AttachController extends Controller
{
    use VibeShowTrait;

    public $trackRepository;

    public function __construct(TrackRepository $trackRepository)
    {
        $this->trackRepository = $trackRepository;
    }

    public function store(Vibe $vibe, $trackApiId)
    {
        $this->authorize('update', $vibe);

        $track = $this->trackRepository->firstOrCreate($trackApiId);
        $pendingVibeTrack = PendingVibeTrack::create([
            'track_id' => $track->id,
            'vibe_id' => $vibe->id,
            'user_id' => auth()->user()->id,
            'attach' => true
        ]);

        broadcast(new PendingAttachVibeTrackCreated($pendingVibeTrack))->toOthers();
        $loadedVibe = app(Playlist::class)->load($vibe);
        return $this->showResponseWithTrack($loadedVibe, $track);
    }

    public function destroy(PendingVibeTrack $pendingVibeTrack)
    {
        $this->authorize('delete', $pendingVibeTrack);

        broadcast(new PendingAttachVibeTrackDeleted($pendingVibeTrack))->toOthers();

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
            ->attach($pendingVibeTrack->track->id, [
                'user_id' => $pendingVibeTrack->user->id,
                'auto_related' => false
            ]);

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
