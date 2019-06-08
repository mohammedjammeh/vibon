<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\Music\Search;
use App\Music\Tracks;
use App\Music\Playlist;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('setAccessToken');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Search $search, Tracks $tracks, Playlist $playlist)
    {
        // $userAPI = app(UserAPI::class);
        // $trackSuggestions = $userAPI->trackSuggestions();
        $trackSuggestions = $search->tracks('Bob Marley and The Wailers');
        return view('home', [
            'apiTracks' => $tracks->check($trackSuggestions),
            'vibes' => $playlist->loadMany(Vibe::all())
        ]);
    }
}
