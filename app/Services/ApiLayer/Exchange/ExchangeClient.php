<?php

declare(strict_types = 1);

namespace App\Services\ApiLayer\Exchange;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;

class ExchangeClient
{
    private const PATH = 'exchangerates_data';

    public function __construct(
        protected PendingRequest $client,
        protected ResponseTransformer $transformer,
    ) {
    }

    /**
     * @return \Illuminate\Support\Collection<\App\Services\ApiLayer\Exchange\Symbol>
     * @throws \Exception
     */
    public function symbols(): Collection
    {
        return $this->transformer->collectSymbols(
            $this->client
                ->throw()
                ->get(
                    sprintf('%s/symbols', self::PATH),
                )
                ->json()
        );
    }

    /**
     * @param string $baseSymbol
     *
     * @return \App\Services\ApiLayer\Exchange\RateList
     */
    public function latest(string $baseSymbol): RateList
    {
        return $this->transformer->collectRates(
            ...$this->client
            ->throw()
            ->get(
                sprintf('%s/latest', self::PATH),
                [
                    'base' => $baseSymbol,
                ]
            )
            ->json()
        );
    }
}
