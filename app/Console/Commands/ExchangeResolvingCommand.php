<?php

declare(strict_types = 1);

namespace App\Console\Commands;

use App\Services\Exchange\ExchangeRepository;
use App\Services\Exchange\ExchangeService;
use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Throwable;

#[AsCommand(name: 'ex:resolve')]
class ExchangeResolvingCommand extends Command
{
    public function handle(
        ExchangeRepository $repository,
        ExchangeService $service,
        LoggerInterface $logger,
    ) {
        foreach ($repository->forExchangeProcessing() as $exchange) {
            try {
                $service->tryResolve($exchange);
            } catch (Throwable $e) {
                $logger->error($e->getMessage(), [
                    'context'     => static::class,
                    'exchange_id' => $exchange->id,
                ]);
            }
        }
    }
}
