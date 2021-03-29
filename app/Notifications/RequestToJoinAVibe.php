<?php

namespace App\Notifications;

use App\Traits\NotificationShowTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RequestToJoinAVibe extends Notification
{
    use Queueable, NotificationShowTrait;

    public $user_id;
    public $vibe_id;

    /**
     * Create a new notification instance.
     *
     * @param $user_id
     * @param $vibe_id
     * @return void
     */
    public function __construct($user_id, $vibe_id)
    {
        $this->user_id = $user_id;
        $this->vibe_id = $vibe_id;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'vibe_id' => $this->vibe_id,
            'user_id' => $this->user_id
        ];
    }

    /**
     * Broadcast notification to front-end user.
     *
     * @param $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        $notification = DatabaseNotification::find($this->id);
        $notification->data = $this->addUserData($notification);

        return new BroadcastMessage([
            'data' => $notification,
        ]);
    }
}
