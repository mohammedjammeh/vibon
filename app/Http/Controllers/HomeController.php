<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Spotify\Search;
use App\Spotify\Tracks;

class HomeController extends Controller

{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Search $search, Tracks $tracks)
    
    {

        $trackSuggestions = $search->tracks('Peter Tosh');

        return view('home', [

            'user' => auth()->user()->load('vibes.tracks'), 

            'apiTracks' => $tracks->check($trackSuggestions), 

            'vibes' => Vibe::all()

        ]);

    }


}
