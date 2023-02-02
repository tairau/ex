<?php

declare(strict_types = 1);

namespace App\Data\Purchase;

use App\Data\Casts\DecimalCast;
use App\Data\Rules\Decimal;
use Brick\Math\BigDecimal;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

class Fund extends Data
{
    public function __construct(
        #[Required]
        #[IntegerType]
        #[MapInputName(input: 'wallet_id')]
        public readonly int $walletId,
        #[Required]
        #[Decimal(min: 0, max: 2)]
        #[Max(10000)]
        #[Min(1)]
        #[WithCast(DecimalCast::class)]
        public readonly BigDecimal $amount,
    ) {
    }
}
