<?php

declare(strict_types = 1);

namespace App\Data\Exchange;

use App\Data\Casts\DateTimeCast;
use App\Data\Casts\DecimalCast;
use App\Data\Rules\Decimal;
use Brick\Math\BigDecimal;
use Carbon\CarbonImmutable;
use DateTimeInterface;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\After;
use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

class Bid extends Data
{
    public function __construct(
        #[Required]
        #[IntegerType]
        #[MapInputName(input: 'wallet_id')]
        public readonly int $walletId,
        #[Required]
        #[IntegerType]
        #[MapInputName(input: 'destination_wallet_id')]
        public readonly int $destinationWalletId,
        #[Required]
        #[Decimal(min: 0, max: 2)]
        #[Max(10000)]
        #[Min(1)]
        #[WithCast(DecimalCast::class)]
        public readonly BigDecimal $amount,
        #[Required]
        #[Decimal(min: 0, max: 4)]
        #[Max(10000)]
        #[Min(0)]
        #[WithCast(DecimalCast::class)]
        public readonly BigDecimal $expected_rate,
        #[Required]
        #[DateFormat(format: DateTimeInterface::ATOM)]
        #[After(date: 'tomorrow')]
        #[MapInputName(input: 'expired_at')]
        #[WithCast(DateTimeCast::class)]
        public readonly CarbonImmutable $expiredAt,
    )
    {
    }
}
