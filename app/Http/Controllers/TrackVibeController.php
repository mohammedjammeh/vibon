<?php

namespace App\Http\Controllers;

use App\Traits\VibeShowTrait;
use App\Vibe;
use App\Track;
use App\MusicAPI\Playlist;
use App\Events\TrackVibeStored;
use App\Events\TrackVibeDestroyed;
use App\Repositories\TrackRepo as TrackRepository;

class TrackVibeController extends Controller
{
    use VibeShowTrait;

    public $trackRepository;

    public function __construct(TrackRepository $trackRepository)
    {
        $this->trackRepository = $trackRepository;
    }

    public function store(Vibe $vibe, $trackApiId)
    {
        $this->authorize('delete', $vibe);

        $track = $this->trackRepository->firstOrCreate($trackApiId);

        $track->vibes()->attach($vibe->id, [
            'user_id' => $vibe->owner->id,
            'auto_related' => false
        ]);

        $this->storeOnPlaylist($vibe, $track);

        broadcast(new TrackVibeStored($track, $vibe))->toOthers();
        $loadedVibe = app(Playlist::class)->load($vibe);
        return $this->showResponseWithTrack($loadedVibe, $track);
    }

    public function storeOnPlaylist($vibe, $track)
    {
        if (!$vibe->auto_dj) {
            return app(Playlist::class)->addTracks($vibe, [$track->api_id]);
        }
    }

    public function destroy(Vibe $vibe, Track $track) 
    {
        $this->authorize('delete', $vibe);

        $vibe->tracks()
            ->wherePivot('auto_related', false)
            ->wherePivot('track_id', $track->id)
            ->detach();

        $this->destroyOnPlaylist($vibe, $track);

        broadcast(new TrackVibeDestroyed($track, $vibe))->toOthers();
        $loadedVibe = app(Playlist::class)->load($vibe);
        return $this->showResponseWithTrack($loadedVibe, $track);
    }

    public function destroyOnPlaylist($vibe, $track)
    {
        if (!$vibe->auto_dj) {
            return app(Playlist::class)->deleteTrack($vibe, $track->api_id);
        }
    }
}
