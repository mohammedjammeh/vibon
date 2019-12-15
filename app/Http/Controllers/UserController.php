<?php

namespace App\Http\Controllers;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('setAccessToken');
    }

    public function vibes()
    {
//        if you are to use the code below, it has to be saved like the one on VibeShowTrait..
//        the right code was not being returned because it was not getting saved..

//        you only need to load the attributes (name, id), not the tracks, etc..
//        actually, this name can be taken from Vue and just send the ids as we will only be showing add/remove form for vibes in that environment just we will only be showing vibes (vibeController@index) in that environment..

//        also later, check out what user can and cannot do if they are not part of vibe.. interesting

//        in the future, you can add a setting to allow user to decide whether they can all see vibes in their environment or just the ones they are actually part of

//        maybe the user should only be able to remove the track from the displayed vibe and not the rest

//        $lol = app(Playlist::class)->loadMany($user['vibes']);
//        dd($lol);

        return auth()->user()->load('vibes')->vibes->where('auto_dj', false)->pluck('id');
    }

    public function attributes()
    {
        return auth()->user();
    }
}
