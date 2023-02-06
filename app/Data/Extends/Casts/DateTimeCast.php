<?php

declare(strict_types = 1);

namespace App\Data\Extends\Casts;

use Carbon\CarbonImmutable;
use Carbon\Exceptions\InvalidFormatException;
use DateTimeInterface;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\DataProperty;

class DateTimeCast implements Cast
{
    /**
     * @param \Spatie\LaravelData\Support\DataProperty $property
     * @param mixed                                    $value
     * @param array                                    $context
     *
     * @return \Carbon\CarbonImmutable
     * @throws \Illuminate\Validation\ValidationException
     */
    public function cast(
        DataProperty $property,
        mixed $value,
        array $context,
    ): CarbonImmutable {
        try {
            return CarbonImmutable::createFromFormat(
                DateTimeInterface::ATOM,
                $value
            );
        } catch (InvalidFormatException $e) {
            throw ValidationException::withMessages([
                $property->name => 'Incorrect date time',
            ]);
        }
    }
}
