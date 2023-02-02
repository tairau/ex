<?php

declare(strict_types = 1);

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class UserResource extends JsonResource
{
    public function __construct(
        private readonly User|MissingValue $user
    ) {
        parent::__construct($user);
    }

    public function toArray($request): array
    {
        return [
            'id'         => $this->user->id,
            'name'       => $this->user->name,
            'email'      => $this->user->email,
            'created_at' => $this->user->created_at->toIso8601String(),
        ];
    }
}
