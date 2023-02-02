<?php

declare(strict_types = 1);

namespace App\Services\ApiLayer\Exchange;

use Illuminate\Support\Collection;

readonly class Symbol
{
    public function __construct(
        public string $symbol,
        public string $name,
    )
    {
    }

    /**
     * @param array $symbols
     *
     * @return \Illuminate\Support\Collection<\App\Services\ApiLayer\Exchange\Symbol>
     */
    public static function wrapToCollection(array $symbols): Collection
    {
        return (new Collection($symbols))
            ->map(function (string $name, string $symbol) {
                return new static($symbol, $name);
            });
    }
}
