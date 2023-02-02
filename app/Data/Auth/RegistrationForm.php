<?php

declare(strict_types = 1);

namespace App\Data\Auth;

use App\Data\Casts\EmailCast;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Password;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

class RegistrationForm extends Data
{
    public function __construct(
        #[Required]
        #[StringType]
        #[Max(255)]
        public readonly string $name,

        #[Required]
        #[Email]
        #[Max(255)]
        #[WithCast(EmailCast::class)]
        public readonly string $email,

        #[Required]
        #[StringType]
        #[Max(255)]
        #[Password]
        public readonly string $password,
    ) {
    }
}
