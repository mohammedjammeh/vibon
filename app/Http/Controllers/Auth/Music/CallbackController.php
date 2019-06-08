<?php

namespace App\Http\Controllers\Auth\Music;

use App\User;
use App\AutoDJ\User as AutoUser;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Music\User as UserAPI;


class CallbackController extends Controller
{
    public function spotifyAuth(Request $request)
    {
        app('SpotifySession')->requestAccessToken($request['code']);
        $accessToken = app('SpotifySession')->getAccessToken();
        $refreshToken = app('SpotifySession')->getRefreshToken();

        $userAPI = app(UserAPI::class);
        $userAPI->setUnauthenticatedAccessToken($accessToken);
        $user = $this->storeOrUpdateUserDetails($userAPI, User::SPOTIFY, $accessToken, $refreshToken);
        return $this->authenticateAndStoreTracks($user);
    }

    public function storeOrUpdateUserDetails($userAPI, $api, $accessToken, $refreshToken)
    {
        $user = User::firstOrNew([
            'username' => $userAPI->details()->id
        ]);
        $user->username = $userAPI->details()->id;
        $user->email = $userAPI->details()->email;
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
        return redirect(route('index'));
    }
}
