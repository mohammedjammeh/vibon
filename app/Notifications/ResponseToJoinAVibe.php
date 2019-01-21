<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ResponseToJoinAVibe extends Notification
{
    use Queueable;

    public $vibe_id;
    public $response;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($vibe_id, $response)
    {
        $this->vibe_id = $vibe_id;
        $this->response = $response;
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
            'response' => $this->response
        ];
    }
}
