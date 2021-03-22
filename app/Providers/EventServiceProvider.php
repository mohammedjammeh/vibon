<?php

namespace App\Providers;

use App\Events\JoinRequestAccepted;
use App\Events\JoinRequestRejected;
use App\Events\PendingAttachVibeTracksAccepted;
use App\Events\PendingAttachVibeTracksRejected;
use App\Events\PendingAttachVibeTracksRespondedTo;
use App\Events\PendingDetachVibeTracksAccepted;
use App\Events\PendingDetachVibeTracksRejected;
use App\Events\PendingDetachVibeTracksRespondedTo;
use App\Events\UserJoinedVibe;
use App\Events\UserLeftVibe;
use App\Events\UserRemovedFromVibe;
use App\Listeners\AttachAcceptedPendingAttachVibeTracks;
use App\Listeners\DeletePendingAttachVibeTracks;
use App\Listeners\DeletePendingDetachVibeTracks;
use App\Listeners\DetachAcceptedPendingDetachVibeTracks;
use App\Listeners\NotifyPendingAttachVibeTracksUsers;
use App\Listeners\NotifyPendingDetachVibeTracksUsers;
use App\Listeners\SendJoinRequestAcceptedNotification;
use App\Listeners\SendJoinRequestRejectedNotification;
use App\Listeners\SendPendingAttachVibeTracksAcceptedNotifications;
use App\Listeners\SendPendingAttachVibeTracksRejectedNotifications;
use App\Listeners\SendPendingDetachVibeTracksAcceptedNotifications;
use App\Listeners\SendPendingDetachVibeTracksRejectedNotifications;
use App\Listeners\SendRemovedFromVibeNotification;
use App\Listeners\SendUserJoinedVibeNotification;
use App\Listeners\SendUserLeftVibeNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\UserCreated;
use App\Listeners\SendUserCreatedNotification;
use App\Events\VibeCreated;
use App\Listeners\StoreAutoVibeTracks;
use App\Events\VibeUpdated;
use App\Listeners\UpdateAutoVibeTracks;
use App\Events\JoinRequestSent;
use App\Listeners\SendJoinRequestNotification;
use App\Events\JoinRequestCancelled;
use App\Listeners\CancelJoinRequestNotification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        UserCreated::class => [
            SendUserCreatedNotification::class,
        ],

        VibeCreated::class => [
            StoreAutoVibeTracks::class,
        ],
        VibeUpdated::class => [
            UpdateAutoVibeTracks::class,
        ],

        JoinRequestSent::class => [
            SendJoinRequestNotification::class,
        ],
        JoinRequestCancelled::class => [
            CancelJoinRequestNotification::class,
        ],
        JoinRequestAccepted::class => [
            SendJoinRequestAcceptedNotification::class,
        ],
        JoinRequestRejected::class => [
            SendJoinRequestRejectedNotification::class,
        ],

        UserJoinedVibe::class => [
            SendUserJoinedVibeNotification::class,
        ],
        UserLeftVibe::class => [
          SendUserLeftVibeNotification::class,
        ],
        UserRemovedFromVibe::class => [
            SendRemovedFromVibeNotification::class,
        ],

        PendingAttachVibeTracksRespondedTo::class => [
            AttachAcceptedPendingAttachVibeTracks::class,
            NotifyPendingAttachVibeTracksUsers::class,
            DeletePendingAttachVibeTracks::class,
        ],
        PendingAttachVibeTracksAccepted::class => [
            SendPendingAttachVibeTracksAcceptedNotifications::class,
        ],
        PendingAttachVibeTracksRejected::class => [
            SendPendingAttachVibeTracksRejectedNotifications::class,
        ],

        PendingDetachVibeTracksRespondedTo::class => [
            DetachAcceptedPendingDetachVibeTracks::class,
            NotifyPendingDetachVibeTracksUsers::class,
            DeletePendingDetachVibeTracks::class,
        ],
        PendingDetachVibeTracksAccepted::class => [
            SendPendingDetachVibeTracksAcceptedNotifications::class,
        ],
        PendingDetachVibeTracksRejected::class => [
            SendPendingDetachVibeTracksRejectedNotifications::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
