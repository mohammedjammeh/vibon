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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    
    {

        $user = User::find(Auth::id());

        $userVibesTracks = $user::with('vibes.tracks')->where('id', Auth::id())->first();

        $responseNotifications = $user->notifications->where('type', 'App\Notifications\ResponseToJoinAVibe');

        $requestNotifications = $user->unreadNotifications->where('type', 'App\Notifications\RequestToJoinAVibe');

        $vibes = Vibe::all();






        $trackRecommendations = $this->spotifyAPI()->search('Bob Marley', 'track')->tracks->items;



        foreach ($trackRecommendations as $trackRecommendation) {

            $trackRecommendation->belongs_to_user_vibes = array();


            foreach ($userVibesTracks['vibes'] as $userVibe) {
                
                for ($i=0; $i < count($userVibe->tracks); $i++) { 

                    if($trackRecommendation->id == $userVibe->tracks[$i]->api_id) {

                        $trackRecommendation->belongs_to_user_vibes[] = $userVibe->id;

                        $trackRecommendation->vibon_id = $userVibe->tracks[$i]->id;

                    } 

                }

            }

        }




        return view('home', [

            'userVibesTracks' => $userVibesTracks, 

            'responseNotifications' => $responseNotifications, 

            'requestNotifications' => $requestNotifications, 

            'trackRecommendations' => $trackRecommendations, 

            'vibes' => $vibes

        ]);

    }
}
