<?php

declare(strict_types = 1);

namespace App\Services\ApiLayer\Exchange;

use Illuminate\Support\Collection;

class ResponseTransformer
{
    public function collectSymbols(array $symbols): Collection
    {
        return (new Collection($symbols))
            ->map(function (string $name, string $symbol) {
                return new Symbol($symbol, $name);
            });
    }

    public function collectRates(string $base, string $date, array $rates, ...$props): RateList
    {
        return new RateList(
            $base,
            $date,
            (new Collection($rates))
                ->map(function (float $rate, string $symbol) {
                    return new Rate($symbol, $rate);
                })
        );
    }
}
