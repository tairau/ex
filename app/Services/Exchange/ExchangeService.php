<?php

declare(strict_types = 1);

namespace App\Services\Exchange;

use App\Data\Exchange\Bid;
use App\Events\ExchangeCreatedEvent;
use App\Models\Exchange;
use App\Models\User;
use App\Services\Database\Transactional;
use App\Services\Exchange\Exceptions\DoesntHaveOrderedRatesException;
use App\Services\Rate\RateRepository;
use App\Services\Wallet\WalletRepository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Validation\ValidationException;

class ExchangeService
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
        private readonly WalletRepository $walletRepository,
        private readonly ExchangeRepository $exchangeRepository,
        private readonly RateRepository $rateRepository,
        private readonly Transactional $transactional,
    ) {
    }

    /**
     * @param \App\Models\User       $user
     * @param \App\Data\Exchange\Bid $bid
     *
     * @return \App\Models\Exchange
     * @throws \Illuminate\Validation\ValidationException
     */
    public function bid(User $user, Bid $bid): Exchange
    {
        $wallet = $this->walletRepository->walletByOwner(
            $bid->walletId,
            $user
        );

        $destinationWallet = $this->walletRepository->walletByOwner(
            $bid->destinationWalletId,
            $user
        );

        if ($destinationWallet->isSameCurrency($wallet)) {
            throw ValidationException::withMessages([
                'destination_wallet_id' => 'Destination wallet has same currency',
            ]);
        }

        if ($wallet->balance->isLessThan($bid->amount)) {
            throw ValidationException::withMessages([
                'amount' => 'Insufficient funds in the wallet',
            ]);
        }

        $exchange = $this->exchangeRepository->create(
            $bid,
            $user,
            $wallet,
            $destinationWallet
        );

        $this->dispatcher->dispatch(new ExchangeCreatedEvent($exchange));

        return $exchange;
    }

    /**
     * @param \App\Models\Exchange $exchange
     *
     * @return void
     * @throws \Throwable
     */
    public function tryResolve(Exchange $exchange): void
    {
        $transaction = function () use ($exchange) {
            $exchange = $this->exchangeRepository->lockedExchange($exchange);

            if ($exchange === null) {
                return;
            }

            $destinationWallet = $this->exchangeRepository
                ->lockedDestinationWallet($exchange);

            $wallet = $this->exchangeRepository->lockedWallet($exchange);

            $rate =
                $this->rateRepository->actualRate($wallet, $destinationWallet);

            if ($rate === null) {
                throw new DoesntHaveOrderedRatesException();
            }

            if ($rate->rate->isGreaterThan($exchange->expected_rate)) {
                return;
            }

            if ($wallet->balance->isLessThan($exchange->amount)) {
                return;
            }

            $convertedAmount = $rate->convert($exchange->amount);
            $wallet->removeFromBalance($exchange->amount);
            $destinationWallet->addToBalance($convertedAmount);

            $this->walletRepository->persist($wallet);
            $this->walletRepository->persist($destinationWallet);
            $this->exchangeRepository->applyRateAndClose($exchange, $rate);
        };

        $this->transactional->wrap($transaction);

        $this->exchangeRepository->refresh($exchange);
    }

    /**
     * @param \App\Models\User $user
     * @param int              $exchangeId
     *
     * @return void
     */
    public function cancel(User $user, int $exchangeId): void
    {
        $exchange = $this->exchangeRepository
            ->notProcessedById($user, $exchangeId);

        $this->exchangeRepository->delete($exchange);
    }
}
