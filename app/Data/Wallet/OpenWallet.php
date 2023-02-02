<?php

declare(strict_types = 1);

namespace App\Data\Wallet;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class OpenWallet extends Data
{
    public function __construct(
        #[Required]
        #[IntegerType]
        #[MapInputName(input: 'currency_id')]
        public readonly int $currencyId,
    ) {
    }
}
