<?php

namespace App\Traits;

use App\PendingVibeTrack;
use App\Policies\VibePolicy;
use App\Track;

trait VibeShowTrait
{
    public function showResponse($loadedVibe, $message = '')
    {
        $loadedVibe->destroyable = App(VibePolicy::class)->delete(auth()->user(), $loadedVibe);
        $loadedVibe->requests = $loadedVibe->joinRequests;
        $loadedVibe->currentUserIsAMember = $loadedVibe->hasMember(auth()->user());
        $loadedVibe->hasJoinRequestFromUser = $loadedVibe->hasJoinRequestFrom(auth()->user());
        $loadedVibe->joinRequestFromUser = $loadedVibe->joinRequestFrom(auth()->user());
        $loadedVibe->notifications = $loadedVibe->notifications();
        $loadedVibe->api_tracks = $loadedVibe->api_tracks->map(function($loadedTrack) use($loadedVibe) {
            return $this->updateTrackInfo($loadedTrack, $loadedVibe);
        });

        return ['vibe' => $loadedVibe, 'message' => $message];
    }

    public function updateTracksVibonInfo($apiTracks)
    {
        return collect($apiTracks)->each(function ($apiTrack) {
            return $this->updateTrackVibonInfo($apiTrack);
        });
    }

    protected function updateTrackInfo($loadedTrack, $vibe)
    {
        $track = $vibe->tracks->where('api_id', $loadedTrack->id)->first();
        $loadedTrack->votes_count = $track->votesCountOn($vibe);
        $loadedTrack->is_voted_by_user = $track->isVotedByAuthUserOn($vibe);
        $loadedTrack = $this->updateTrackVibonInfo($loadedTrack);
        return $loadedTrack;
    }

    protected function updateTrackVibonInfo($loadedTrack)
    {
        $loadedTrack->vibon_id = $this->getVibonID($loadedTrack);
        $loadedTrack->vibes = $this->getVibesIDs($loadedTrack);
        $loadedTrack->pending_vibes = PendingVibeTrack::where('track_id', $loadedTrack->vibon_id)
            ->pluck('vibe_id')->toArray();

        return $loadedTrack;
    }

    protected function getVibonID($loadedTrack)
    {
        $track = Track::where('api_id', $loadedTrack->id)->first();
        return !is_null($track) ? $track->id : null;
    }

    protected function getVibesIDs($loadedTrack)
    {
        $user = $this->userVibesManualTracks();
        $vibesTracks = $user['vibes']->pluck('tracks', 'id');

        $userTracksVibes = $vibesTracks->filter(function ($tracks) use($loadedTrack) {
            $tracksIDs = $tracks->pluck('api_id');
            return $tracksIDs->contains($loadedTrack->id);
        });

        return $userTracksVibes->keys()->toArray();
    }

    protected function userVibesManualTracks()
    {
        return auth()->user()->load(['vibes.tracks' => function($query) {
            $query->where('auto_related', false);
        }]);
    }
}