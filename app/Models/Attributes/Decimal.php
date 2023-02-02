<?php

declare(strict_types = 1);

namespace App\Models\Attributes;

use Brick\Math\BigDecimal;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class Decimal implements CastsAttributes
{

    public function get($model, string $key, $value, array $attributes)
    {
        return BigDecimal::of($value);
    }

    public function set($model, string $key, $value, array $attributes): string
    {
        return (string)$value;
    }
}
