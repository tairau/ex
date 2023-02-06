<?php

namespace App\Listeners;


use App\Events\ExchangeCreatedEvent;
use App\Services\Exchange\Exceptions\ResolveExchangeException;
use App\Services\Exchange\ExchangeService;

class TryResolveExchangeNow
{
    public function __construct(
        private readonly ExchangeService $exchangeService
    ) {
    }

    public function handle(ExchangeCreatedEvent $event): void
    {
        try {
            $this->exchangeService->tryResolve($event->exchange);
        } catch (ResolveExchangeException) {
        }
    }
}
