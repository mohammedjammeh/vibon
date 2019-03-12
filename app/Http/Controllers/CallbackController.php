<?php

namespace App\Http\Controllers;

use App\User;
use App\AutoDJ\User as UserAuto;
use SpotifyWebAPI\SpotifyWebAPI;
use Illuminate\Support\Facades\Auth;

class CallbackController extends Controller
{
    public function storeOrUpdateUserDetails($username, $email, $api, $accessToken, $refreshToken)
    {
        $user = User::firstOrNew(['username' => $username]);
        $user->username = $username;
        $user->email = $email; 
        $user->api = $api;
        $user->access_token = $accessToken;
        $user->refresh_token = $refreshToken;
        $user->token_set_at = date("Y-m-d H:i:s");
        $user->save();
        return $user;
    }

    public function completeAuth($user)
    {
        Auth::login($user, true);
        if ($user->tracks->isEmpty()) {
            $userAuto = app(UserAuto::class);
            $userAuto->storeTracks();
        }
        return redirect('home');
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
        return $this->completeAuth($user);
    }

    public function appleAuth()
    {
    }
}
