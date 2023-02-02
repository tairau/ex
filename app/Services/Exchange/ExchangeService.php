<?php

declare(strict_types = 1);

namespace App\Services\Exchange;

use App\Data\Exchange\Bid;
use App\Events\ExchangeCreatedEvent;
use App\Models\Exchange;
use App\Models\Rate;
use App\Models\User;
use App\Services\Exchange\Exceptions\DoesntHaveOrderedRatesException;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;
use Illuminate\Validation\ValidationException;

readonly class ExchangeService
{
    public function __construct(private Dispatcher $dispatcher) { }

    /**
     * @param \App\Models\User       $user
     * @param \App\Data\Exchange\Bid $bid
     *
     * @return \App\Models\Exchange
     * @throws \Illuminate\Validation\ValidationException
     */
    public function bid(User $user, Bid $bid): Exchange
    {
        /** @var \App\Models\Wallet $wallet */
        $wallet = $user
            ->wallets()
            ->where('id', $bid->walletId)
            ->firstOrFail();

        /** @var \App\Models\Wallet $destinationWallet */
        $destinationWallet = $user
            ->wallets()
            ->where('id', $bid->destinationWalletId)
            ->firstOrFail();

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

        $exchange = new Exchange([
            'amount'        => $bid->amount,
            'expected_rate' => $bid->expected_rate,
            'expired_at'    => $bid->expiredAt,
        ]);

        $exchange->user()->associate($user);
        $exchange->wallet()->associate($wallet);
        $exchange->destinationWallet()->associate($destinationWallet);
        $exchange->save();

        $this->dispatcher->dispatch(new ExchangeCreatedEvent($exchange));

        return $exchange;
    }

    /**
     * @param \App\Models\Exchange $exchange
     *
     * @return void
     */
    public function tryResolve(Exchange $exchange): void
    {
        $transaction = function () use ($exchange) {
            /** @var \App\Models\Exchange $exchange */
            $exchange = $exchange
                ->newModelQuery()
                ->active()
                ->notProcessed()
                ->where('id', $exchange->id)
                ->lockForUpdate()
                ->first();

            if ($exchange === null) {
                return;
            }

            /** @var \App\Models\Wallet $destinationWallet */
            $destinationWallet = $exchange
                ->destinationWallet()
                ->lockForUpdate()
                ->first();

            /** @var \App\Models\Wallet $wallet */
            $wallet = $exchange
                ->wallet()
                ->lockForUpdate()
                ->first();

            /** @var \App\Models\Rate|null $rate */
            $rate = Rate::query()
                ->where('from_currency_id', $wallet->currency_id)
                ->where('to_currency_id', $destinationWallet->currency_id)
                ->where('date', today())
                ->first();

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
            $wallet->save();
            $destinationWallet->addToBalance($convertedAmount);
            $destinationWallet->save();
            $exchange->exchanged_at = now();
            $exchange->rate()->associate($rate);
            $exchange->save();
        };

        DB::transaction($transaction);

        $exchange->refresh();
    }

    /**
     * @param \App\Models\User $user
     * @param int              $exchangeId
     *
     * @return void
     */
    public function cancel(User $user, int $exchangeId): void
    {
        /** @var \App\Models\Exchange|null $exchange */
        $exchange = $user
            ->exchanges()
            ->where('id', $exchangeId)
            ->notProcessed()
            ->firstOrFail();

        $exchange->delete();
    }

    /**
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginateForUser(User $user): LengthAwarePaginator
    {
        return $user
            ->exchanges()
            ->with('rate')
            ->withTrashed()
            ->paginate();
    }

    /**
     * @return \Illuminate\Support\LazyCollection<\App\Models\Exchange>
     */
    public function forExchangeProcessing(): LazyCollection
    {
        return Exchange::query()
            ->orderBy('created_at')
            ->notProcessed()
            ->active()
            ->lazyById();
    }
}
