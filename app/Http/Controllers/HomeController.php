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
        $this->middleware('auth');
        $this->middleware('spotifyAuth');
    }


    public function content($userVibesTracks, $notifications, $trackRecommendations) {
        $homeContent = array('userVibesTracks' => $userVibesTracks, 'notifications' => $notifications, 'trackRecommendations' => $trackRecommendations);
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
        } 

        $userVibesTracks = $user::with('vibes.tracks')->where('id', Auth::id())->first();
        $notifications = $user->notifications;
        $trackRecommendations = $this->spotifyAPI()->search('Bob Marley', 'track')->tracks->items;

        return $this->content($userVibesTracks, $notifications, $trackRecommendations);
    }
}
