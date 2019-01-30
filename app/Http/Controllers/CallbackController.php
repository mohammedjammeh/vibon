<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class CallbackController extends Controller

{

    public function spotifyAuth()

    {

        app('Spotify')->requestAccessToken($_GET['code']);

        $accessToken = app('Spotify')->getAccessToken();

        Session::put('accessToken', $accessToken);


        $this->checkUserApiId();


        return redirect()->back();
        
    }





    public function checkUserApiId() 

    {

        $user = User::findOrFail(Auth()->user()->id);

        if ($user->api_id == null) 

        {

            $user->api_id = $this->spotifyAPI()->me()->id;

            $user->save();

        }

    }

}
