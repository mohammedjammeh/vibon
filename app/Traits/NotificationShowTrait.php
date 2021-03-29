<?php

namespace App\Traits;

use App\MusicAPI\Tracks;
use App\Track;
use App\User;

trait NotificationShowTrait
{
    public function addUserData($notification)
    {
        $notificationData = $notification->data;
        $notificationData['user_display_name'] = User::find($notificationData['user_id'])->display_name;

        return $notificationData;
    }

    public function addTrackData($notification)
    {
        $notificationData = $notification->data;
        $track = Track::find($notificationData['track_id']);
        $notificationData['track_name'] = collect(app(Tracks::class)->load([$track]))->first()->name;

        return $notificationData;
    }
}