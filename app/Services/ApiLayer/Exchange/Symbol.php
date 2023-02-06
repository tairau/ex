<?php

declare(strict_types = 1);

namespace App\Services\ApiLayer\Exchange;

readonly class Symbol
{
    public function __construct(
        public string $symbol,
        public string $name,
    ) {
    }
}
