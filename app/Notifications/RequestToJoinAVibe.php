<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RequestToJoinAVibe extends Notification
{
    use Queueable;

    public $requester_id;
    public $vibe_id;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($requester_id, $vibe_id)
    {
        $this->requester_id = $requester_id;
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
            'vibe_id' => $this->vibe_id,
            'requester_id' => $this->requester_id
        ];
    }
}
