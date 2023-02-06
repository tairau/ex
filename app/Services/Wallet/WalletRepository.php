<?php

declare(strict_types = 1);

namespace App\Services\Wallet;

use App\Models\User;
use App\Models\Wallet;

class WalletRepository
{
    /**
     * @param int              $walletId
     * @param \App\Models\User $owner
     *
     * @return \App\Models\Wallet
     */
    public function walletByOwner(int $walletId, User $owner): Wallet
    {
        /** @var Wallet */
        return $owner
            ->wallets()
            ->where('id', $walletId)
            ->firstOrFail();
    }

    /**
     * @param \App\Models\Wallet $wallet
     *
     * @return void
     */
    public function persist(Wallet $wallet): void
    {
        $wallet->save();
    }
}
