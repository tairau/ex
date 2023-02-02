<?php

declare(strict_types = 1);

namespace App\Console\Commands;

use App\Services\Exchange\ExchangeService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'ex:resolve')]
class ExchangeResolvingCommand extends Command
{
    public function handle(
        ExchangeService $exchangeClient,
    ) {
        foreach ($exchangeClient->forExchangeProcessing() as $exchange) {
            $exchangeClient->tryResolve($exchange);
        }
    }
}
