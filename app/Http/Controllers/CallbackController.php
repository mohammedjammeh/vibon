<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use App\User;
use Illuminate\Support\Facades\Auth;

class CallbackController extends Controller
{
    public function spotifyAuth()
    {
        app('Spotify')->requestAccessToken($_GET['code']);
        $accessToken = app('Spotify')->getAccessToken();

        Session::put('accessToken', $accessToken);
        Session::forget('credentialsToken');

        $user = User::findOrFail(Auth::id());
        if ($user->api_id == null) {
            $user->api_id = $this->spotifyAPI()->me()->id;
            $user->save();
        }

        return redirect('home');
    }
}
