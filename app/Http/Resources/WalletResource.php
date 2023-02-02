<?php

declare(strict_types = 1);

namespace App\Http\Resources;

use App\Models\Wallet;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class WalletResource extends JsonResource
{
    public function __construct(private readonly Wallet|MissingValue $wallet)
    {
        parent::__construct($wallet);
    }

    public function toArray($request): array
    {
        return [
            'id'       => $this->wallet->id,
            'user'     => new UserResource($this->whenLoaded('user')),
            'currency' => new CurrencyResource($this->whenLoaded('currency')),
            'balance'  => (string)$this->wallet->balance,
        ];
    }
}
