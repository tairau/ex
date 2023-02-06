<?php

declare(strict_types = 1);

namespace App\Services\Purchase;

use App\Data\Purchase\Fund;
use App\Models\User;
use App\Models\Wallet;
use App\Services\Database\Transactional;

class PurchaseRepository
{
    public function __construct(private readonly Transactional $transactional) { }

    /**
     * @param \App\Models\User        $user
     * @param \App\Data\Purchase\Fund $fund
     *
     * @return \App\Models\Wallet
     * @throws \Throwable
     */
    public function add(
        User $user,
        Fund $fund
    ): Wallet {
        $transaction = function () use ($user, $fund): Wallet {
            /** @var \App\Models\Wallet $wallet */
            $wallet = $user
                ->wallets()
                ->where('id', $fund->walletId)
                ->lockForUpdate()
                ->firstOrFail();

            $wallet->addToBalance($fund->amount);
            $wallet->save();

            return $wallet;
        };

        return $this->transactional->wrap($transaction);
    }
}
