<?php

declare(strict_types = 1);

namespace App\Events;

use App\Models\Exchange;

readonly class ExchangeCreatedEvent
{
    public function __construct(public Exchange $exchange) { }
}
