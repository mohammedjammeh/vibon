<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\MusicAPI\Search;
use App\MusicAPI\Tracks;
use App\MusicAPI\Playlist;
use App\MusicAPI\User;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('setAccessToken');
    }

    /**
     * Show the application dashboard.
     *
     * @param \App\MusicAPI\Search  $search
     * @param \App\MusicAPI\Tracks  $tracks
     * @param \App\MusicAPI\Playlist  $playlist
     * @param \App\MusicAPI\User  $user
     * @return \Illuminate\Http\Response
     */
    public function index(Search $search, Tracks $tracks, Playlist $playlist, User $user)
    {
        $trackSuggestions = $user->trackSuggestions();
//        $trackSuggestions = $search->tracks('Bob Marley and The Wailers');
        return view('home', [
            'apiTracks' => $tracks->check($trackSuggestions),
            'vibes' => $playlist->loadMany(Vibe::all())
        ]);
    }
}
