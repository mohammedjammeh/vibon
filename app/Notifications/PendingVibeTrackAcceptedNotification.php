<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PendingVibeTrackAcceptedNotification extends Notification
{
    use Queueable;

    public $vibe_id;
    public $track_id;

    /**
     * Create a new notification instance.
     *
     * @param $vibe_id,
     * @param $track_id,
     * @return void
     */
    public function __construct($vibe_id, $track_id)
    {
        $this->vibe_id = $vibe_id;
        $this->track_id = $track_id;
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
            'track_id' => $this->track_id
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

        return new BroadcastMessage([
            'data' => $notification,
        ]);
    }
}
