<?php

declare(strict_types = 1);

namespace App\Data\Extends\Casts;

use Brick\Math\BigDecimal;
use Brick\Math\Exception\MathException;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\DataProperty;

class DecimalCast implements Cast
{
    /**
     * @param \Spatie\LaravelData\Support\DataProperty $property
     * @param mixed                                    $value
     * @param array                                    $context
     *
     * @return \Brick\Math\BigDecimal
     * @throws \Illuminate\Validation\ValidationException
     */
    public function cast(
        DataProperty $property,
        mixed $value,
        array $context,
    ): BigDecimal {
        try {
            return BigDecimal::of($value);
        } catch (MathException $e) {
            throw ValidationException::withMessages([
                $property->name => 'Incorrect amount',
            ]);
        }
    }
}
