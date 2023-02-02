<?php

namespace App\Models;

use App\Models\Attributes\Decimal;
use Brick\Math\BigDecimal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int                        $id
 * @property int                        $from_currency_id
 * @property \App\Models\Currency       $fromCurrency
 * @property \App\Models\Currency       $toCurrency
 * @property int                        $to_currency_id
 * @property BigDecimal                 $rate
 * @property \Carbon\Carbon             $date
 * @property \Illuminate\Support\Carbon $created_at
 */
class Rate extends Model
{
    protected $fillable = [
        'rate',
        'date',
    ];

    protected $casts = [
        'rate' => Decimal::class,
    ];

    protected $dates = [
        'date',
    ];

    public function toCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'to_currency_id');
    }

    public function fromCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'from_currency_id');
    }

    public function convert(BigDecimal $amount): BigDecimal
    {
        return $this->rate->multipliedBy($amount);
    }
}
