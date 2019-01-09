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

    public function content($vibes, $tracks) {
        $homeContent = array('vibes' => $vibes, 'tracks' => $tracks);
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
            $vibes = Vibe::all();
            $tracks = $this->spotifyAPI()->search('two love', 'track')->tracks->items;
            return $this->content($vibes, $tracks);
        } 

        $vibes = $user->vibes()->where('user_id', Auth::id())->where('vibe_dj', 1)->get();
        $tracks = $this->spotifyAPI()->search('Bob Marley', 'track')->tracks->items;
        return $this->content($vibes, $tracks);
    }
}
