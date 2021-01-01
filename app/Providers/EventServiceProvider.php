<?php

namespace App\Providers;

use App\Events\JoinRequestAccepted;
use App\Events\JoinRequestRejected;
use App\Events\PendingVibeTrackAccepted;
use App\Events\PendingVibeTrackRejected;
use App\Events\UserJoinedVibe;
use App\Events\UserLeftVibe;
use App\Events\UserRemovedFromVibe;
use App\Listeners\SendJoinRequestAcceptedNotification;
use App\Listeners\SendJoinRequestRejectedNotification;
use App\Listeners\SendPendingVibeTrackAcceptedNotification;
use App\Listeners\SendPendingVibeTrackRejectedNotification;
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

        PendingVibeTrackAccepted::class => [
            SendPendingVibeTrackAcceptedNotification::class,
        ],
        PendingVibeTrackRejected::class => [
            SendPendingVibeTrackRejectedNotification::class,
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
