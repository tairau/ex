<?php

declare(strict_types = 1);

namespace App\Services\Currency;


use App\Models\Currency;
use Illuminate\Database\Eloquent\Collection;

class CurrencyService
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection<\App\Models\Currency>
     */
    public function all(): Collection
    {
        return Currency::all();
    }
}
