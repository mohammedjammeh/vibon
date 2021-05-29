<?php

namespace App\Traits;

use App\PendingVibeTrack;
use App\Policies\VibePolicy;
use App\Track;
use App\Repositories\TrackRepo as TrackRepository;

trait VibeShowTrait
{
    public function showResponseWithTrack($loadedVibe, $track)
    {
        $response = $this->showResponse($loadedVibe);
        $response['track'] = $track;
        return $response;
    }

    public function showResponse($loadedVibe, $message = '')
    {
        $loadedVibe->destroyable = App(VibePolicy::class)->delete(auth()->user(), $loadedVibe);
        $loadedVibe->requests = $loadedVibe->joinRequests;
        $loadedVibe->currentUserIsAMember = $loadedVibe->hasMember(auth()->user());
        $loadedVibe->hasJoinRequestFromUser = $loadedVibe->hasJoinRequestFrom(auth()->user());
        $loadedVibe->joinRequestFromUser = $loadedVibe->joinRequestFrom(auth()->user());
        $loadedVibe->notifications = $loadedVibe->notifications();

        $loadedVibe->api_tracks->each(function ($tracks) use ($loadedVibe) {
            $tracks->each(function ($track) use ($loadedVibe) {
                $this->updateTrackInfo($track, $loadedVibe);
            });
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
        $track = Track::firstOrCreate(['api_id' => $loadedTrack->id]);

        $loadedTrack->votes_count = $track->votesCountOn($vibe);
        $loadedTrack->is_voted_by_user = $track->isVotedByAuthUserOn($vibe);
        $loadedTrack->pending_to_attach_user = $this->pendingTrackUser($vibe->pendingTracksToAttach, $track);
        $loadedTrack->pending_to_detach_user = $this->pendingTrackUser($vibe->pendingTracksToDetach, $track);
        $loadedTrack = $this->updateTrackVibonInfo($loadedTrack);

        return $loadedTrack;
    }

    protected function updateTrackVibonInfo($loadedTrack)
    {
        $loadedTrack->vibon_id = $this->getVibonID($loadedTrack);
        $loadedTrack->vibes = $this->getVibesIDs($loadedTrack);
        $loadedTrack->pending_vibes_to_attach = PendingVibeTrack::where('track_id', $loadedTrack->vibon_id)->where('attach', true)->pluck('vibe_id')->toArray();
        $loadedTrack->pending_vibes_to_detach = PendingVibeTrack::where('track_id', $loadedTrack->vibon_id)->where('attach', false)->pluck('vibe_id')->toArray();

        return $loadedTrack;
    }

    protected function getVibonID($loadedTrack)
    {
        $track = app(TrackRepository::class)->firstOrCreate($loadedTrack->id);
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

    protected function pendingTrackUser($pendingTracks, $track)
    {
        $pendingTrack = $pendingTracks->where('track_id', $track->id)->first();
        if(is_null($pendingTrack)) {
            return null;
        }

        return $pendingTrack->user->display_name;
    }
}