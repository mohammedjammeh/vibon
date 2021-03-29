<?php

namespace App\Notifications;

use App\Traits\NotificationShowTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PendingDetachVibeTracksRejectedNotification extends Notification
{
    use Queueable, NotificationShowTrait;

    public $vibe_id;
    public $track_id;
    public $attach;

    /**
     * Create a new notification instance.
     *
     * @param $vibe_id,
     * @param $track_id,
     * @param $attach
     * @return void
     */
    public function __construct($vibe_id, $track_id, $attach)
    {
        $this->vibe_id = $vibe_id;
        $this->track_id = $track_id;
        $this->attach = $attach;
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
            'track_id' => $this->track_id,
            'attach' => $this->attach
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
        $notification->data = $this->addTrackData($notification);

        return new BroadcastMessage([
            'data' => $notification,
        ]);
    }
}
