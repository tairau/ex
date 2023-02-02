<?php

declare(strict_types = 1);

namespace App\Services\ApiLayer\Exchange;

use Illuminate\Support\Collection;

readonly class RateList
{
    public function __construct(
        public string $symbol,
        public string $date,
        /** @var \Illuminate\Support\Collection<\App\Services\ApiLayer\Exchange\Rate> $rates */
        public Collection $rates
    ) { }
}
