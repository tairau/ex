<?php

declare(strict_types = 1);

namespace App\Data\Extends\Casts;

use Illuminate\Support\Str;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\DataProperty;

class EmailCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $context): string
    {
        return Str::lower($value);
    }
}
