<?php

namespace App\Http\Controllers;

use App\User;
use App\AutoDJ\User as AutoUser;
use SpotifyWebAPI\SpotifyWebAPI;
use Illuminate\Support\Facades\Auth;

class CallbackController extends Controller
{
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
        return $this->authenticateAndStoreTracks($user);
    }

    public function storeOrUpdateUserDetails($username, $email, $api, $accessToken, $refreshToken)
    {
        $user = User::firstOrNew(['username' => $username]);
        $user->username = $username;
        $user->email = $email; 
        $user->api = $api;
        $user->access_token = $accessToken;
        $user->refresh_token = $refreshToken;
        $user->token_set_at = now();
        $user->save();
        return $user;
    }

    public function authenticateAndStoreTracks($user)
    {
        Auth::login($user, true);
        if ($user->tracks->isEmpty()) {
            AutoUser::storeTracks();
        }
        return redirect('home');
    }
}
