<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\User;
use Illuminate\Support\Facades\Auth;

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
    }


    public function content($user, $trackRecommendations) {
        $homeContent = array('user' => $user, 'trackRecommendations' => $trackRecommendations);
        return view('home')->with('homeContent', $homeContent);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::find(Auth::id());

        if (!$user) {
            // $vibes = Vibe::all();
            // $tracks = $this->spotifyAPI()->search('two love', 'track')->tracks->items;

            // return $this->content($vibes, $tracks);
        } 

        $user = $user::with('vibes.tracks')->where('id', Auth::id())->get();



        // dd($user[0]->vibes[0]->tracks[0]->pivot->vibe_id);

        $trackRecommendations = $this->spotifyAPI()->search('Bob Marley', 'track')->tracks->items;
        return $this->content($user, $trackRecommendations);
    }
}
