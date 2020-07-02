<?php

namespace App\Http\Controllers;

use App\Traits\NotificationShowTrait;
use App\MusicAPI\User as UserAPI;

class UserController extends Controller
{
    use NotificationShowTrait;

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
        $notifications =  auth()->user()->notifications->sortByDesc('created_at');
        foreach ($notifications as $notification) {
            $notification->data = $this->updateData($notification);
        }
        return $notifications;
    }
}
