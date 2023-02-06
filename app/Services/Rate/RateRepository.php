<?php

declare(strict_types = 1);

namespace App\Services\Rate;

use App\Models\Currency;
use App\Models\Rate;
use App\Models\Wallet;
use Brick\Math\BigDecimal;
use Carbon\CarbonImmutable;

class RateRepository
{
    public function actualRate(
        Wallet $wallet,
        Wallet $destinationWallet,
    ): Rate|null {
        /** @var \App\Models\Rate|null */
        return Rate::query()
            ->where('from_currency_id', $wallet->currency_id)
            ->where('to_currency_id', $destinationWallet->currency_id)
            ->where('date', today())
            ->first();
    }

    /**
     * @param \App\Models\Currency    $fromCurrency
     * @param \App\Models\Currency    $toCurrency
     * @param \Brick\Math\BigDecimal  $actualRate
     * @param \Carbon\CarbonImmutable $date
     *
     * @return void
     */
    public function upsert(
        Currency $fromCurrency,
        Currency $toCurrency,
        BigDecimal $actualRate,
        CarbonImmutable $date
    ): void {
        Rate::query()->upsert([
            'rate'             => (string)$actualRate,
            'date'             => $date->startOfDay(),
            'from_currency_id' => $fromCurrency->id,
            'to_currency_id'   => $toCurrency->id,
        ], [
            'from_currency_id',
            'to_currency_id',
            'date',
        ]);
    }
}
