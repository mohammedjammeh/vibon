<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RequestToJoinAVibe extends Notification
{
    use Queueable;

    public $requesterID;
    public $vibeID;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($requesterID, $vibeID)
    {
        $this->requesterID = $requesterID;
        $this->vibeID = $vibeID;

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
            'requester_id' => $this->requesterID,
            'vibe_id' => $this->vibeID,
            'accepted' => 0
        ];
    }
}
