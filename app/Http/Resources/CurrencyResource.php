<?php

declare(strict_types = 1);

namespace App\Http\Resources;

use App\Models\Currency;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class CurrencyResource extends JsonResource
{
    public function __construct(
        private readonly Currency|MissingValue $currency
    ) {
        parent::__construct($currency);
    }

    public function toArray($request): array
    {
        return [
            'id'   => $this->currency->id,
            'name' => $this->currency->name,
            'iso'  => $this->currency->iso,
        ];
    }
}
