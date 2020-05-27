<?php

namespace App\Http\Controllers\Auth\MusicAPI;

use App\User;
use App\AutoDJ\User as AutoUser;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\MusicAPI\User as UserAPI;


class CallbackController extends Controller
{
    public function spotifyAuth(Request $request)
    {
        app('SpotifySession')->requestAccessToken($request['code']);
        $accessToken = app('SpotifySession')->getAccessToken();
        $refreshToken = app('SpotifySession')->getRefreshToken();

        $userAPI = app(UserAPI::class);
        $userAPI->setAccessToken($accessToken);

        $user = $this->storeOrUpdateUserDetails($userAPI, User::SPOTIFY, $accessToken, $refreshToken);
        Auth::login($user, true);
        $this->storeTracks($user);
        return redirect(route('index'));
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

    public function storeTracks($user)
    {
        if ($user->tracks->isEmpty()) {
            AutoUser::storeTracks();
        }
    }
}
