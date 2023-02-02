<?php

declare(strict_types = 1);

namespace App\Services\Wallet;

use App\Data\Wallet\OpenWallet;
use App\Models\Currency;
use App\Models\User;
use App\Models\Wallet;
use Brick\Math\BigDecimal;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class WalletService
{
    /**
     * @param \App\Models\User            $user
     * @param \App\Data\Wallet\OpenWallet $openWallet
     *
     * @return \App\Models\Wallet
     * @throws \Illuminate\Validation\ValidationException
     */
    public function open(User $user, OpenWallet $openWallet): Wallet
    {
        /** @var Currency $currency */
        $currency = Currency::query()
            ->findOrFail($openWallet->currencyId);

        $wallet = $user->wallets()
            ->where('currency_id', $currency->id)
            ->first();

        if ($wallet) {
            throw ValidationException::withMessages([
                'currency_id' => 'Wallet already exists',
            ]);
        }

        $wallet = new Wallet([
            'balance' => BigDecimal::zero(),
        ]);
        $wallet->user()->associate($user);
        $wallet->currency()->associate($currency);
        $wallet->save();

        return $wallet;
    }

    public function paginateForUser(User $user): LengthAwarePaginator
    {
        return $user
            ->wallets()
            ->with('currency')
            ->paginate();
    }
}
