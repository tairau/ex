<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\Currency;
use App\Services\ApiLayer\Exchange\ExchangeClient;
use Illuminate\Database\Seeder;

class CurrenciesSeeder extends Seeder
{
    /**
     * @throws \Exception
     */
    public function run(ExchangeClient $client)
    {
        foreach ($client->symbols() as $symbol) {
            Currency::query()->upsert([
                'name'   => $symbol->name,
                'iso' => $symbol->symbol,
            ], 'iso');
        }
    }
}
