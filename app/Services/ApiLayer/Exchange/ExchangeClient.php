<?php

declare(strict_types = 1);

namespace App\Services\ApiLayer\Exchange;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;

final class ExchangeClient
{
    private const PATH = 'exchangerates_data';

    public function __construct(protected PendingRequest $client) {}

    /**
     * @return \Illuminate\Support\Collection<\App\Services\ApiLayer\Exchange\Symbol>
     * @throws \Exception
     */
    public function symbols(): Collection
    {
        return Symbol::wrapToCollection(
            $this->client
                ->throw()
                ->get(
                    sprintf('%s/symbols', self::PATH),
                )
                ->json('symbols', [])
        );
    }

    /**
     * @param string $baseSymbol
     *
     * @return \App\Services\ApiLayer\Exchange\RateList
     */
    public function latest(string $baseSymbol): RateList
    {
        $json = $this->client
            ->throw()
            ->get(
                sprintf('%s/latest', self::PATH),
                [
                    'base' => $baseSymbol,
                ]
            )
            ->json();

        return new RateList(
            $json['base'],
            $json['date'],
            Rate::wrapToCollection($json['rates'])
        );
    }
}
