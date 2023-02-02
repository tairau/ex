<?php

declare(strict_types = 1);

namespace App\Http\Resources;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\JsonResource;

class LengthAwarePaginatorMetaResource extends JsonResource
{
    public function __construct(
        private readonly LengthAwarePaginator $paginator
    ) {
        parent::__construct($paginator);
    }

    public function toArray($request): array
    {
        return [
            'current_page' => $this->paginator->currentPage(),
            'from'         => $this->paginator->firstItem(),
            'last_page'    => $this->paginator->lastPage(),
            'per_page'     => $this->paginator->perPage(),
            'to'           => $this->paginator->lastItem(),
            'total'        => $this->paginator->total(),
        ];
    }
}
