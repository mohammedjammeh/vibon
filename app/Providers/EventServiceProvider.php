<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\UserCreated;
use App\Listeners\SendUserCreatedNotification;
use App\Events\VibeCreated;
use App\Listeners\StoreAutoVibeTracks;
use App\Events\VibeUpdated;
use App\Listeners\UpdateAutoVibeTracks;

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
