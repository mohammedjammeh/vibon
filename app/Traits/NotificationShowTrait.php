<?php

namespace App\Traits;

use App\Notifications\JoinedVibe;
use App\Notifications\LeftVibe;
use App\Notifications\RequestToJoinAVibe;
use App\User;

trait NotificationShowTrait
{
    public function updateData($notification)
    {
        $notificationData = $notification->data;

        if ($notification->type === RequestToJoinAVibe::class || $notification->type === LeftVibe::class || $notification->type === JoinedVibe::class) {
            $notificationData['user_username'] = User::find($notification->data['user_id'])->username;
        }

        return $notificationData;
    }
}