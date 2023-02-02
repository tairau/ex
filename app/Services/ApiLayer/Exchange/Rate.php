<?php

declare(strict_types = 1);

namespace App\Services\ApiLayer\Exchange;

use Illuminate\Support\Collection;

readonly class Rate
{
    public function __construct(public string $symbol, public  float $rate) { }

    public static function wrapToCollection(array $rates): Collection
    {
        return (new Collection($rates))
            ->map(function (float $rate, string $symbol) {
                return new static($symbol, $rate);
            });
    }
}
