<?php

declare(strict_types = 1);

namespace App\Services\ApiLayer\Exchange;

readonly class Rate
{
    public function __construct(public string $symbol, public float $rate) { }
}
