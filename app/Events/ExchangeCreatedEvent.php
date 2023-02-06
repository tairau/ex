<?php

declare(strict_types = 1);

namespace App\Events;

use App\Models\Exchange;

class ExchangeCreatedEvent
{
    public function __construct(public readonly Exchange $exchange) { }
}
