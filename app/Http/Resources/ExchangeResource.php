<?php

declare(strict_types = 1);

namespace App\Http\Resources;

use App\Models\Exchange;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class ExchangeResource extends JsonResource
{
    public function __construct(private readonly Exchange|MissingValue $exchange
    ) {
        parent::__construct($exchange);
    }

    public function toArray($request): array
    {
        return [
            'id'                => $this->exchange->id,
            'wallet'            => new WalletResource(
                $this->whenLoaded('wallet')
            ),
            'destinationWallet' => new WalletResource(
                $this->whenLoaded('destinationWallet')
            ),
            'amount'            => (string)$this->exchange->amount,
            'expected_rate'     => (string)$this->exchange->expected_rate,
            'expired_at'        => $this->exchange->expired_at->toIso8601String(),
            'exchanged_at'      => $this->exchange->exchanged_at?->toIso8601String(),
            'created_at'        => $this->exchange->created_at->toIso8601String(),
            'deleted_at'        => $this->exchange->deleted_at?->toIso8601String(),
        ];
    }
}
