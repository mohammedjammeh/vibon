<?php

namespace App\Notifications;

use App\Traits\NotificationShowTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class JoinedVibe extends Notification
{
    use Queueable, NotificationShowTrait;

    public $user;
    public $vibe;

    /**
     * Create a new notification instance.
     *
     * @param $user
     * @param $vibe
     * @return void
     */
    public function __construct($user, $vibe)
    {
        $this->user = $user;
        $this->vibe = $vibe;
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
            'vibe_id' => $this->vibe,
            'user_id' => $this->user
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
