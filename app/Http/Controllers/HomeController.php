<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Music\Search;
use App\Music\Tracks;

class HomeController extends Controller

{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Search $search, Tracks $tracks)
    
    {

        $trackSuggestions = $search->tracks('Bob Marley and The Wailers');

        return view('home', [

            'user' => auth()->user()->load('vibes.tracks'), 

            'apiTracks' => $tracks->check($trackSuggestions), 

            'vibes' => Vibe::all()

        ]);

    }


}
