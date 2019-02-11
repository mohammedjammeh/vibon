<?php

namespace App\Http\Controllers;

use App\User;
use SpotifyWebAPI\SpotifyWebAPI;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class CallbackController extends Controller
{
    public function updateUserDetailsForAPI($id, $accessToken, $refreshToken)
    {
        $user = User::findOrFail(Auth()->user()->id);
        $user->api_id = $id;
        $user->api_access_token = $accessToken;
        $user->api_refresh_token = $refreshToken;
        $user->api_token_set_at = date("Y-m-d H:i:s");
        $user->save();
    }

    public function spotifyAuth()
    {
        app('Spotify')->requestAccessToken($_GET['code']);
        $accessToken = app('Spotify')->getAccessToken();
        $refreshToken = app('Spotify')->getRefreshToken();

        $api = new SpotifyWebAPI();
        $api->setAccessToken($accessToken);
        $id = $api->me()->id;

        $this->updateUserDetailsForAPI($id, $accessToken, $refreshToken);
        return redirect()->back();
    }


}
