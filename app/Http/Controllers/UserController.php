<?php

namespace App\Http\Controllers;

use App\Notifications\JoinedVibe;
use App\Notifications\LeftVibe;
use App\Notifications\RequestToJoinAVibe;
use App\User;
use App\MusicAPI\User as UserAPI;

class UserController extends Controller
{
    public function vibes()
    {
        return auth()->user()->load('vibes')->vibes->where('auto_dj', false)->pluck('id');
    }

    public function attributes()
    {
        app(UserAPI::class)->setAccessToken(auth()->user()->access_token);
        return auth()->user();
    }

    public function notifications()
    {
        $notifications =  auth()->user()->notifications;
        foreach ($notifications as $notification) {
            $notification->data = $this->updateNotification($notification);
        }

        return $notifications;
    }

    protected function updateNotification($notification)
    {
        $notificationData = $notification->data;

        if($notification->type === RequestToJoinAVibe::class) {
            $notificationData['requester_username'] = User::find($notification->data['requester_id'])->username;
        }
        if ($notification->type === LeftVibe::class || $notification->type === JoinedVibe::class) {
            $notificationData['user_username'] = User::find($notification->data['user_id'])->username;
        }

        return $notificationData;
    }
}
