<?php

namespace App\Http\Controllers;

use App\User;
use SpotifyWebAPI\SpotifyWebAPI;
use Illuminate\Support\Facades\Auth;

class CallbackController extends Controller
{
    public function storeOrUpdateUserDetails($username, $email, $api, $accessToken, $refreshToken)
    {
        $user = new User();
        if($user->findBy($username)) {
            $user = $user->findBy($username);
        }
        $user->username = $username;
        $user->email = $email; 
        $user->api = $api;
        $user->access_token = $accessToken;
        $user->refresh_token = $refreshToken;
        $user->token_set_at = date("Y-m-d H:i:s");
        $user->save();
        return $user;
    }

    public function spotifyAuth()
    {
        app('Spotify')->requestAccessToken($_GET['code']);
        $api = new SpotifyWebAPI();
        $api->setAccessToken(app('Spotify')->getAccessToken());

        $user = $this->storeOrUpdateUserDetails(
            $api->me()->id,
            $api->me()->email, 
            User::SPOTIFY,
            app('Spotify')->getAccessToken(), 
            app('Spotify')->getRefreshToken()
        );

        Auth::login($user, true);
        return redirect(session('authRequestMadeAt'));
    }

    public function appleAuth()
    {
    }
}
