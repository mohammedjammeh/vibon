<?php

namespace App\Music;

use Illuminate\Support\Arr;
use App\Music\InterfaceAPI;
use App\Music\Tracks;

class User
{
    public const TOP_TRACK = 1;
    public const RECENT_TRACK = 2;

    protected $api;

    public function __construct(InterfaceAPI $interfaceAPI)
    {
        $this->api = $interfaceAPI;
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
        $tracks = collect($tracksList)->pluck('track');
        $trackGroups = $tracks->groupBy('id')->toArray();
        $trackGroupsPlayedMoreThanOnce = Arr::where($trackGroups, function ($value, $key) {
            return count($value) > 1;
        });
        $tracksPlayedMoreThanOnce = Arr::flatten($trackGroupsPlayedMoreThanOnce);
        $uniqueTracks = collect($tracksPlayedMoreThanOnce)->unique('id');
        return $uniqueTracks->toArray();   
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
            'seed_tracks' => array($userRecentTracks[1], $userRecentTracks[2], $userRecentTracks[3]),
            'limit' => 10
        ]);
        return $suggestions->tracks;
    }
}
