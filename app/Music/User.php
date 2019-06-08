<?php

namespace App\Music;

use Illuminate\Support\Arr;

class User
{
    public const TOP_TRACK = 1;
    public const RECENT_TRACK = 2;

    public $api;

    public function __construct(InterfaceAPI $interfaceAPI)
    {
        $this->api = $interfaceAPI;
    }

    public function details()
    {
        return $this->api->getUser();
    }

    public function authorise()
    {
        return $this->api->authorise();
    }

    public function setAuthenticatedAccessToken()
    {
        return $this->api->setAuthenticatedUserAccessToken();
    }

    public function setUnauthenticatedAccessToken($accessToken)
    {
        return $this->api->setUnauthenticatedUserAccessToken($accessToken);
    }

    public function topTracks()
    {
        $tracksList = $this->api->getUserTopTracks();
        collect($tracksList)->map(function ($track) {
            $track->type = self::TOP_TRACK;
        });
        return $tracksList;
    }   

    public function recentTopTracks()
    {
        $tracksList = $this->api->getUserRecentTracks();
        $allTracks = collect($tracksList)->pluck('track');
        $trackGroups = $allTracks->groupBy('id')->toArray();
        $trackGroupsPlayedMoreThanOnce = Arr::where($trackGroups, function ($value, $key) {
            return count($value) > 1;
        });
        $tracksPlayedMoreThanOnce = Arr::flatten($trackGroupsPlayedMoreThanOnce);
        $tracks = collect($tracksPlayedMoreThanOnce)->unique('id');
        $tracks->map(function ($track) {
            $track->type = self::RECENT_TRACK;
        });
        return $tracks->toArray();   
    }

    public function recentTopAndOverallTopTracks()
    {
        $tracksList = array_merge($this->topTracks(), $this->recentTopTracks());
        $tracks = collect($tracksList)->unique('id');
        $tracks->values()->all();
        return $tracks;
    }

    public function trackSuggestions()
    {
        $userTopTracks = collect($this->topTracks())->pluck('id');
        $userRecentTracks = collect($this->recentTopTracks())->pluck('id');
        
        $tracksAPI = app(Tracks::class);
        $suggestions = $tracksAPI->getRecommendations([
            'target_popularity' => 40,
            'seed_tracks' => array($userRecentTracks[1], $userTopTracks[2], $userRecentTracks[3]),
            'limit' => 10
        ]);
        return $suggestions->tracks;
    }
}
