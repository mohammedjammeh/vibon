<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Spotify\PlaylistTracks;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()

    {

        $this->middleware('spotifySession');

        $this->middleware('auth');

        $this->middleware('spotifyAuth');

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    
    {

        $trackSuggestions = $this->spotifyAPI()->search('Peter Tosh', 'track')->tracks->items;

        return view('home', [

            'user' => auth()->user()->load('vibes.tracks'), 

            'apiTracks' => PlaylistTracks::check($trackSuggestions), 

            'vibes' => Vibe::all()

        ]);

    }
}
