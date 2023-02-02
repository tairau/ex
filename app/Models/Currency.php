<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int                                      $id
 * @property string                                   $name
 * @property string                                   $iso
 * @property \Illuminate\Database\Eloquent\Collection $rates
 */
class Currency extends Model
{
    protected $fillable = [
        'name',
        'iso',
    ];

    public function rates(): HasMany
    {
        return $this->hasMany(Rate::class, 'to_currency_id');
    }
}
