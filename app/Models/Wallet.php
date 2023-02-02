<?php

namespace App\Models;

use App\Models\Attributes\Decimal;
use Brick\Math\BigDecimal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int                  $id
 * @property \App\Models\User     $user
 * @property \App\Models\Currency $currency
 * @property int                  $currency_id
 * @property BigDecimal           $balance
 */
class Wallet extends Model
{
    protected $fillable = [
        'balance',
    ];

    protected $casts = [
        'balance' => Decimal::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function addToBalance(BigDecimal $amount)
    {
        $this->balance = $this->balance->plus($amount);
    }

    public function removeFromBalance(BigDecimal $amount)
    {
        $this->balance = $this->balance->minus($amount);
    }

    public function isSameCurrency(Wallet $wallet): bool
    {
        return $this->currency_id === $wallet->currency_id;
    }
}
