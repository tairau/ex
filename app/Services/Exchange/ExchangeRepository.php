<?php

declare(strict_types = 1);

namespace App\Services\Exchange;

use App\Data\Exchange\Bid;
use App\Models\Exchange;
use App\Models\Rate;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\LazyCollection;

class ExchangeRepository
{
    /**
     * @param \App\Data\Exchange\Bid $bid
     * @param \App\Models\User       $user
     * @param \App\Models\Wallet     $wallet
     * @param \App\Models\Wallet     $destinationWallet
     *
     * @return \App\Models\Exchange
     */
    public function create(
        Bid $bid,
        User $user,
        Wallet $wallet,
        Wallet $destinationWallet
    ): Exchange {
        $exchange = new Exchange([
            'amount'        => $bid->amount,
            'expected_rate' => $bid->expectedRate,
            'expired_at'    => $bid->expiredAt,
        ]);

        $exchange->user()->associate($user);
        $exchange->wallet()->associate($wallet);
        $exchange->destinationWallet()->associate($destinationWallet);
        $this->persist($exchange);

        return $exchange;
    }

    /**
     * @param \App\Models\Exchange $exchange
     *
     * @return \App\Models\Exchange|null
     */
    public function lockedExchange(Exchange $exchange): Exchange|null
    {
        /** @var \App\Models\Exchange|null */
        return $exchange
            ->newModelQuery()
            ->active()
            ->notProcessed()
            ->where('id', $exchange->id)
            ->lockForUpdate()
            ->first();
    }

    /**
     * @param \App\Models\Exchange $exchange
     *
     * @return \App\Models\Wallet
     */
    public function lockedDestinationWallet(Exchange $exchange): Wallet
    {
        /** @var \App\Models\Wallet */
        return $exchange
            ->destinationWallet()
            ->lockForUpdate()
            ->first();
    }

    /**
     * @param \App\Models\Exchange $exchange
     *
     * @return \App\Models\Wallet
     */
    public function lockedWallet(Exchange $exchange): Wallet
    {
        /** @var \App\Models\Wallet */
        return $exchange
            ->wallet()
            ->lockForUpdate()
            ->first();
    }

    /**
     * @param \App\Models\Exchange $exchange
     *
     * @return void
     */
    public function persist(Exchange $exchange): void
    {
        $exchange->save();
    }

    /**
     * @param \App\Models\User $user
     * @param int              $exchangeId
     *
     * @return \App\Models\Exchange
     */
    public function notProcessedById(User $user, int $exchangeId): Exchange
    {
        /** @var \App\Models\Exchange */
        return $user
            ->exchanges()
            ->where('id', $exchangeId)
            ->notProcessed()
            ->firstOrFail();
    }

    /**
     * @param \App\Models\Exchange $exchange
     *
     * @return void
     */
    public function delete(Exchange $exchange): void
    {
        $exchange->delete();
    }

    /**
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginatedByUser(User $user): LengthAwarePaginator
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

    public function applyRateAndClose(Exchange $exchange, Rate $rate): void
    {
        $exchange->exchanged_at = now();
        $exchange->rate()->associate($rate);

        $this->persist($exchange);
    }

    public function refresh(Exchange $exchange): Exchange
    {
        return $exchange->refresh();
    }
}
