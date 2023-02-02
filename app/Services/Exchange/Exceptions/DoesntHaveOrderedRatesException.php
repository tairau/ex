<?php

declare(strict_types = 1);

namespace App\Services\Exchange\Exceptions;

class DoesntHaveOrderedRatesException extends ResolveExchangeException
{
    protected $message = "Doesn't have ordered rates";
}
