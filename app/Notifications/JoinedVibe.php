<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class JoinedVibe extends Notification
{
    use Queueable;

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
        return ['database'];
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
}
