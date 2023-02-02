<?php

namespace App\Providers;

use App\Events\ExchangeCreatedEvent;
use App\Listeners\TryResolveExchangeNow;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        ExchangeCreatedEvent::class => [
            TryResolveExchangeNow::class,
        ],
    ];

    public function boot()
    {
        //
    }

    public function shouldDiscoverEvents()
    {
        return false;
    }
}
