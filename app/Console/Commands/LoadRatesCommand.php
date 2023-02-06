<?php

declare(strict_types = 1);

namespace App\Console\Commands;

use App\Services\ApiLayer\Exchange\ExchangeClient;
use App\Services\Currency\CurrencyRepository;
use App\Services\Rate\RateRepository;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'rates:load')]
class LoadRatesCommand extends Command
{
    public function handle(
        CurrencyRepository $currencyRepository,
        ExchangeClient $exchangeClient,
        RateRepository $rateRepository,
    ) {
        $currencies = $currencyRepository->all()->keyBy('iso');

        foreach ($currencies as $currency) {
            $rates = $exchangeClient->latest($currency->iso);

            foreach ($rates->rates as $rate) {
                $toCurrency = $currencies->get($rate->symbol);

                if ($toCurrency === null) {
                    continue;
                }
                $date = CarbonImmutable::parse($rates->date)->startOfDay();

                $actualRate = BigDecimal::of($rate->rate);

                if ($actualRate->getScale() > 4) {
                    $actualRate = $actualRate->toScale(4, RoundingMode::HALF_UP);
                }

                $rateRepository->upsert($currency, $toCurrency, $actualRate, $date);
            }
        }
    }
}
