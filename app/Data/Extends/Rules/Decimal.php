<?php

declare(strict_types = 1);

namespace App\Data\Extends\Rules;

use Attribute;
use Spatie\LaravelData\Attributes\Validation\StringValidationAttribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Decimal extends StringValidationAttribute
{
    private int $min;
    private int|null $max;
    public function __construct(int $min, int|null $max = null)
    {
        $this->min = $min;
        $this->max = $max;
    }

    public static function keyword(): string
    {
        return 'decimal';
    }

    public function parameters(): array
    {
        return [$this->min, $this->max];
    }
}
