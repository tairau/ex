<?php

namespace App\Models;

use App\Models\Attributes\Decimal;
use Brick\Math\BigDecimal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int                             $id
 * @property BigDecimal                      $amount
 * @property BigDecimal                      $expected_rate
 * @property \App\Models\User                $user
 * @property \App\Models\Wallet              $wallet
 * @property int                             $wallet_id
 * @property int                             $rate_id
 * @property \App\Models\Rate|null           $rate
 * @property \App\Models\Wallet              $destinationWallet
 * @property int                             $destination_wallet_id
 * @property \Illuminate\Support\Carbon      $expired_at
 * @property \Illuminate\Support\Carbon|null $exchanged_at
 * @property \Illuminate\Support\Carbon      $created_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Exchange extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'amount',
        'expected_rate',
        'expired_at',
        'exchanged_at',
    ];

    protected $casts = [
        'amount'        => Decimal::class,
        'expected_rate' => Decimal::class,
    ];

    protected $dates = [
        'expired_at',
        'exchanged_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rate(): BelongsTo
    {
        return $this->belongsTo(Rate::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function destinationWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'destination_wallet_id');
    }

    public function scopeNotProcessed(Builder $builder)
    {
        $builder->whereNull('exchanged_at');
    }

    public function scopeActive(Builder $builder)
    {
        $builder->where('expired_at', '>', now());
    }
}
